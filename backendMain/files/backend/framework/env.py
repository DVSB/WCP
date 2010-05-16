'''
Created on 05.05.2010

@author: toby
'''

class env(object):
    
    def __init__(self, db, config): 
        self.db = db
        self.config = config
        self.cpnr = db.cpnr
        self.wcfnr = db.wcfnr