from framework.basishandler import *
import subprocess

class counthome(basishandler):
    
    def run(self):  
        users = self.db.queryDict('SELECT * \
                                   FROM wcf' + self.db.wcfnr + '_user user \
                                   JOIN cp' + self.db.cpnr + '_user cpuser ON (user.userID = cpuser.userID) ')      

        option = str(self.db.querySingle("SELECT optionID \
                                      FROM wcf" + self.db.wcfnr + "_user_option \
                                      WHERE optionName = 'diskspaceUsed' AND packageID = " + self.config.wcf.package_id)[0])      
        
        dirsDB = self.config.getSection('cp.backendpaths.countpaths')
        
        odirs = ''
        for dir in dirsDB:
            if dirsDB[dir][0] == 'textarea':
                odirs += dirsDB[dir][1]
                
        for user in users:   
        
            dirs = odirs.replace("{USERNAME}", user["username"])
            dirs = dirs.replace("{GUID}", str(user["guid"]))
            dirs = dirs.replace("{HOMEDIR_PREFIX}", self.config.get('cp.global', 'homedir_prefix'))
        
            dirs = dirs.split("\r\n")
            
            for dir in dirs:
                dir = dir.split(":")
                
                params = ["du", "-s", "-0"]
                
                if dir[1] != '':
                    dir[1] = dir[1].split(",")
                    
                    for exclude in dir[1]:
                        params.append("--exclude="+exclude)
                        
                params.append(dir[0])

                bytes = subprocess.Popen(params, stdout=subprocess.PIPE).communicate()[0]
                
                bytes = str(bytes.split("\t")[0])
                
                self.db.query("UPDATE wcf" + self.db.wcfnr + "_user_option_value \
                               SET userOption" + option + " = '" + bytes + "' \
                               WHERE userID = " + str(user["userID"]))      
                    
        return 'success'
            
                    