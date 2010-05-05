'''
Created on 03.05.2010

@author: toby
'''

import os
from re import *

class basisFileContainer(object):
    '''
    classdocs
    '''
    def __init__(self, domain, env):
        self.env = env
        self.domain = domain
        
        self.file = None
        self.vhostpath = None
        self.fileprefix = None
        self.reloadcommand = None
        self.logpath = {'access': None, 'error': None}
        
        self.setVars()
        
        #TODO: errorhandling, if vars are not set
        
        self.vhostpath = os.path.abspath(self.vhostpath)
        
        if self.file == None:
            self.file =  self.vhostpath + '/' + self.fileprefix + '_' + self.domain.domainID + '_' + self.domain.domainname + '.conf'
        
        self.file = os.path.abspath(self.file)
        
    def setVars(self):
        return "implement me"
    
    def create(self):
        self.parse()
    
    def update(self):
        self.file = self.getFile()
        self.parse()
                
    def delete(self):
        file = self.getFile()
        
        if file <> False:
            os.remove(file)
        
    def getFile(self):
        if os.access(self.file, os.F_OK):
            return self.file
        else:
            #maybe renamed, domainname is wrong?
            files = os.listdir(self.vhostpath)
            
            for file in files:
                if search(self.fileprefix + '_' + self.domain.domainID + '_.*', file) <> None:
                    return self.vhostpath + '/' + file
                
        return False
    
    def parse(self):
        #TODO: cheetah template parser