from modules.basishandler import basishandler
from framework.functions import os, parseUser, parseOptions, getUserOptions, getActiveUsers
import glob

class counthome(basishandler):
    
    def run(self):  
        users = getActiveUsers(self.env.config)
        
        dirsDB = self.env.config.getSection('cp.backendpaths.countpaths')
        
        option = getUserOptions(self.env.config, ['diskspaceUsed'])[0][0]
        
        odirs = ''
        for dir in dirsDB:
           d = dir.values()[0]
           if d[0] == 'textarea':
               odirs += d[1].strip() + "\n"
        
        # parse dirs with options
        odirs = parseOptions(odirs, self.env.config)     
        
        for user in users:   
            # parse dirs with userdata
            dirs = parseUser(odirs, user)
            dirs = dirs.splitlines()
            
            bytes = 0.0
            
            for dir in dirs:
                dir = dir.split(":")
                
                # empty dirs are not good
                if dir[0] is '':
                    continue
                
                exclude = []
                if len(dir) > 1 and dir[1] != '': # are there dirs to exclude?
                    exclude = dir[1].split(",")
                
                bytes += self.getDirSize(dir[0], exclude)
            
            # turn bytes to megabytes
            bytes /= 1024*1024 

            # update user
            self.env.db.query("UPDATE  wcf" + self.env.wcfnr + "_user_option_value \
                           SET     userOption" + str(option) + " = '" + str(bytes) + "' \
                           WHERE   userID = " + str(user["userID"]))      
                    
        return 'success'
            
            
    def getDirSize(self, dir, exclude = []):
        
        dir_size = 0
        
        # if * is in path, get all matching dirs
        if "*" in dir:
            dirs = glob.glob(dir)
            
            for dir in dirs:
                if dir in exclude: # do not enter excluded dirs
                    continue
                dir_size += self.getDirSize(dir, exclude)
                
            return dir_size

        # walk given dir, count all files and call func recursivly for subdirs
        for (path, dirs, files) in os.walk(dir):
            for dir in dirs:    
                if dir in exclude:
                    dirs.remove(dir) # remove ignored dirs
                    
            for file in files:
                file = os.path.join(path, file)
                if os.path.exists(file) is True: # ignore missing files (dangling symlinks?!)
                    dir_size += os.path.getsize(file)

        return dir_size
                