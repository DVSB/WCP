import os.path
from re import *

class wcfconfig(object):
    
    def __init__(self, path):
        self.path = path
        self.getCPConf()
        self.getWCFConf()
    
    def getCPConf(self):   
        if os.path.exists(self.path + '/config.inc.php') == False:
            raise StandardError, self.path + '/config.inc.php not found!'
        
        # first get all neccessary data from cp
        f = file(self.path + '/config.inc.php', 'r')
        data = f.read()

        self.cpnr = self.get("^.*define\('CP_N', '(.*)'\);$", data)
        self.wcfdir = self.path + '/' + self.get("^.*define\('RELATIVE_WCF_DIR', RELATIVE_CP_DIR.'(.*)'\);$", data)
        self.package_id = self.get("^.*define\('PACKAGE_ID', (\d+)\);$", data)

    def getWCFConf(self):
        if os.path.exists(self.wcfdir + 'config.inc.php') == False:
            raise StandardError, self.wcfdir + 'config.inc.php not found!'
        
        # read wcf-config to get DB access data and wcf-nr
        f = file(self.wcfdir + 'config.inc.php', 'r')
        data = f.read()
            
        self.dbHost = self.get("^\$dbHost = '(.*)';$", data)
        self.dbUser = self.get("^\$dbUser = '(.*)';$", data)
        self.dbPassword = self.get("^\$dbPassword = '(.*)';$", data)
        self.dbName = self.get("^\$dbName = '(.*)';$", data)
        self.dbCharset = self.get("^\$dbCharset = '(.*)';$", data)
        self.wcfnr = self.get("^.*define\('WCF_N', (\d+)\);$", data)
                
    def get(self, pattern, data):
        result = search(pattern, data, MULTILINE)
        return result.group(1)
        