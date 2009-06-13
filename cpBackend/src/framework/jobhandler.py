import imp
import sys

class jobhandler(object):
    
    def __init__ (self, db, config):
        self.db = db
        self.config = config
        
        self.timeExec = ['immediately','hourchange','daychange','weekchange','monthchange','yearchange']
    
    # get all available modules from db
    def loadModules(self):
        configs = self.db.query('SELECT optionName, categoryName, optionType, optionValue \
                                FROM cp'+self.db.cpnr+'_jobhandler')
        
        