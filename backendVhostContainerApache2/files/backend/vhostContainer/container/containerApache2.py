'''
Created on 03.05.2010

@author: toby
'''

import os, sys
from re import *
from Cheetah.Template import Template
from subprocess import call
from containerDefault import containerDefault

class containerApache2(containerDefault):
    '''
    classdocs
    '''
    def setVars(self):
        self.template = self.env.config.get('containerApache2template')
        self.vhostpath = os.path.abspath(self.env.config.get('containerApache2vhostpath'))
        self.fileprefix = self.env.config.get('containerApache2fileprefix')
        self.reloadcommand = self.env.config.get('containerApache2reloadcommand')
        
        self.vars['logpath']['access'] = self.env.config.get('containerApache2logaccess')
        self.vars['logpath']['error'] = self.env.config.get('containerApache2logerror')
        self.vars.update(self.domain.vhostContainer)
        self.vars.update(self.domain.domain)
        self.vars.update(self.domain.user)
        
        self.file =  os.path.abspath(self.vhostpath + '/' + self.fileprefix + '_' + self.domain.domainID + '_' + self.domain.domainname + '.conf')
    
    def createDomain(self):
        self.parse()
        self.writeFile()
        self.reloadServer()
    
    #update is just the same as create
    def updateDomain(self):
        self.create()
                
    def deleteDomain(self):
        self.getFilePath()
        
        if self.file <> False:
            os.remove(self.file)
            self.reloadServer()
            
    def parse(self):        
        self.parsedTemplate = Template(self.template, searchList=[self.vars])
        
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
