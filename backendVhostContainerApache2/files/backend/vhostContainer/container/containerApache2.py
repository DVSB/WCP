'''
Created on 03.05.2010

@author: toby
'''

import os, sys
from re import *
from Cheetah.Template import Template
from subprocess import call
from vhostContainer.container.containerDefault import containerDefault

class containerApache2(containerDefault):
    '''
    classdocs
    '''
    def setVars(self):
        self.template = self.env.config.get('containerApache2template')
        self.vhostpath = os.path.abspath(self.env.config.get('containerApache2vhostpath'))
        self.fileprefix = self.env.config.get('containerApache2fileprefix')
        self.ipandportprefix = self.env.config.get('containerApache2ipandportprefix')
        self.reloadcommand = self.env.config.get('containerApache2reloadcommand')
        
        self.vars['logpath'] = self.env.config.get('containerApache2logPath')
        self.vars.update(self.domain.vhostContainer.vhost)
        self.vars.update(self.domain.domain)
        self.vars.update(self.domain.user.user)
        
        self.file =  os.path.abspath(self.vhostpath + '/' + self.fileprefix + '_' + self.domain.domainID + '_' + self.domain.get("domainname") + '.conf')
    
    def createDomain(self):
        self.parse()
        self.writeFile()
    
    #update is nearly the same as create
    def updateDomain(self):
        self.deleteDomain()
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
            f = file(self.file, 'w')
            f.write(str(self.parsedTemplate))
            f.close()
            
    def finishContainer(self):
        self.writeIPandPort()    
        self.reloadServer()
        
    def writeIPandPort(self):
        ipAndPort = ""
        if self.domain.vhostContainer.get('addListenStatement'):
            ipAndPort += "Listen " + str(self.domain.vhostContainer.get('ipAddress')) + ":" + str(self.domain.vhostContainer.get('port')) + "\n"
            
        if self.domain.vhostContainer.get('addNameStatement'):
            ipAndPort += "NameVirtualHost " + str(self.domain.vhostContainer.get('ipAddress')) + ":" + str(self.domain.vhostContainer.get('port')) + "\n"
            
        if ipAndPort:
            f = file(os.path.abspath(self.vhostpath + '/' + self.ipandportprefix + '_' + str(self.domain.vhostContainer.get('ipAddress')) + "." + str(self.domain.vhostContainer.get('port')) + '.conf'), 'w')
            f.write(ipAndPort)
            f.close()
            
    def reloadServer(self):
        try:
            retcode = call(self.reloadcommand, shell=True)
            if retcode <> 0:
                self.env.logger.append("restart apache2 failed")
                return False
            else:
                self.env.logger.append("restart apache2 ok")
                return True
        except OSError, e:
            elf.env.logger.append("restart apache2 failed")
            return False
