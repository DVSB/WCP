'''
Created on 03.05.2010

@author: toby
'''

import os, sys
from re import *
from Cheetah.Template import Template
from subprocess import call

class basisFileContainer(object):
    '''
    classdocs
    '''
    def __init__(self, domain, env, vhostType = None):
        self.env = env
        self.domain = domain
        self.vhostType = vhostType
        
        self.vars = {}
        self.file = None
        self.vhostpath = None
        self.fileprefix = None
        self.reloadcommand = None
        self.template = None
        self.vars['logpath'] = {'access': None, 'error': None}
        self.parsedTemplate = None
        
        self.setVars()
        
        #TODO: errorhandling, if vars are not set
        
        self.vhostpath = os.path.abspath(self.vhostpath)
        
        if self.file == None:
            self.file =  self.vhostpath + '/' + self.fileprefix + '_' + self.domain.domainID + '_' + self.domain.domainname + '.conf'
        
        self.file = os.path.abspath(self.file)
        
    def setVars(self):
        self.template = self.env.config.get(self.vhostType + 'template')
        self.vhostpath = self.env.config.get(self.vhostType + 'vhostpath')
        self.fileprefix = self.env.config.get(self.vhostType + 'fileprefix')
        self.reloadcommand = self.env.config.get(self.vhostType + 'reloadcommand')
        
        self.vars['logpath']['access'] = self.env.config.get(self.vhostType + 'logaccess')
        self.vars['logpath']['error'] = self.env.config.get(self.vhostType + 'logerror')
        self.vars.update(self.domain.vhostContainer)
        self.vars.update(self.domain.domain)
        self.vars.update(self.domain.user)
    
    def parse(self):        
        self.parsedTemplate = Template(self.template, searchList=[self.vars])
    
    def create(self):
        self.parse()
        self.writeFile()
        self.reloadServer()
    
    #update is just the same as create
    def update(self):
        self.create()
                
    def delete(self):
        self.getFilePath()
        
        if self.file <> False:
            os.remove(self.file)
            self.reloadServer()
        
    def getFilePath(self):
        if os.access(self.file, os.F_OK):
            return self.file
        else:
            #maybe renamed, domainname is wrong?
            files = os.listdir(self.vhostpath)
            
            for file in files:
                if search(self.fileprefix + '_' + self.domain.domainID + '_.*', file) <> None:
                    return self.vhostpath + '/' + file
                
        return False
        
    def writeFile(self):
        if self.parsedTemplate <> None:
            file = file(self.file, 'w')
            file.write(self.parsedTemplate)
            file.close()
            
    def reloadServer(self):
        try:
            retcode = call(self.reloadcommand, shell=True)
            if retcode <> 0:
                return "restart failed"
            else:
                return "ok"
        except OSError, e:
            return "restart failed"
