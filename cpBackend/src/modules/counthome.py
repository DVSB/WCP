from framework.basishandler import *
import os
from os.path import join, getsize

class counthome(basishandler):
    
    def run(self):  
        users = self.db.queryDict('SELECT * \
                                   FROM   wcf' + self.db.wcfnr + '_user user \
                                   JOIN   cp' + self.db.cpnr + '_user cpuser ON (user.userID = cpuser.userID)')      

        option = str(self.db.querySingle("SELECT optionID \
                                          FROM   wcf" + self.db.wcfnr + "_user_option \
                                          WHERE  optionName = 'diskspaceUsed' AND packageID = " + self.config.wcf.package_id)[0])      
        
        dirsDB = self.config.getSection('cp.backendpaths.countpaths')
        
        odirs = ''
        for dir in dirsDB:
            if dirsDB[dir][0] == 'textarea':
                odirs += dirsDB[dir][1]
                
        odirs = parseOptions(odirs, self.config)        
        
        for user in users:   
        
            dirs = parseUser(odir, user)
            dirs = dirs.split("\r\n")
            
            bytes = 0
            
            for dir in dirs:
                dir = dir.split(":")
                
                exclude = []
                if len(dir) > 1 and dir[1] != '':
                    exclude = dir[1].split(",")
                
                self.getDirSize(dir[0], exclude)
                           
            self.db.query("UPDATE  wcf" + self.db.wcfnr + "_user_option_value \
                           SET     userOption" + option + " = '" + str(bytes) + "' \
                           WHERE   userID = " + str(user["userID"]))      
                    
        return 'success'
            
            
    def getDirSize(self, dir, exclude = []):
        
        dir_size = 0
        
        if "*" in dir:
             print "search for every match"
        
        for (path, dirs, files) in os.walk(dir, True, None, True):
            for file in files:
                dir_size = sum(getsize(join(path, file)) for file in files)
                
            for dir in dirs:
                if join(path, dir) not in exclude:
                    dir_size += self.getDirSize(dir, exclude)
                
        return dir_size
                