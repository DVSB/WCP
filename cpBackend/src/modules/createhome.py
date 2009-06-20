from framework.basishandler import *
from framework.functions import *

class createhome(basishandler):
    
    def run(self):  
        user = self.db.queryDict('SELECT * \
                                  FROM wcf' + self.db.wcfnr + '_user user \
                                  JOIN cp' + self.db.cpnr + '_user cpuser ON (user.userID = cpuser.userID) \
                                  WHERE user.userID = ' + self.data['userID'])[0]      
        
        dirs = self.config.get('cp', 'home_dirs')

        dirs = dirs.replace("{USERNAME}", user["username"])
        dirs = dirs.replace("{GUID}", str(user["guid"]))
        dirs = dirs.replace("{HOMEDIR_PREFIX}", self.config.get('cp', 'homedir_prefix'))
        
        dirs = dirs.split("\r\n")
        
        for dir in dirs:
            dir = dir.split(":")
            
            dir[2] = int(dir[2], 8)
            
            dir[3] = dir[3].split(".")
            dir[3][0] = getUID(dir[3][0])
            dir[3][1] = getGID(dir[3][1])
            
            print dir
            
            mkPath(dir[0], dir[1], dir[2], dir[3][0], dir[3][1])
            
        return 'success'
            
                    