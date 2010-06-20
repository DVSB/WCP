'''
Created on 05.05.2010

@author: toby
'''
from framework.wcfconfig import wcfconfig
from framework.database import database
from framework.configuration import configuration
from framework.logger import logger

class env(object):
    
    def __init__(self, path):
        self.wcfconfig = wcfconfig(path)
        
        self.db = database(wcfconfig)
        self.config = configuration(db, wcfconfig) 
        self.logger = logger(db)
        
        self.cpnr = self.db.cpnr
        self.wcfnr = self.db.wcfnr
        
    def close(self):
        self.logger.close()