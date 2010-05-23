from modules.basishandler import basishandler
from framework.functions import parseOptions, parseUser, mkPath, getUID, getGID

class createhome(basishandler):
    
    def run(self):  
        user = self.env.db.queryDict('SELECT * \
                                  FROM wcf' + self.env.wcfnr + '_user user \
                                  JOIN cp' + self.env.cpnr + '_user cpuser ON (user.userID = cpuser.userID) \
                                  WHERE user.userID = ' + self.data['userID'])[0]      
        
        dirsDB = self.env.config.getSection('cp.backendpaths.createpaths')
        
        dirs = ''
        for dir in dirsDB:
            if dirsDB[dir][0] == 'textarea':
                dirs += dirsDB[dir][1]
        
        dirs = parseOptions(dirs, self.env.config)
        dirs = parseUser(dirs, user)
        dirs = dirs.split("\r\n")
        
        for dir in dirs:
            dir = dir.split(":")
            
            dir[2] = int(dir[2], 8)
            
            dir[3] = dir[3].split(".")
            dir[3][0] = getUID(dir[3][0])
            dir[3][1] = getGID(dir[3][1])
            
            mkPath(dir[0], dir[1], dir[2], dir[3][0], dir[3][1])
            
        return 'success'
            
                    