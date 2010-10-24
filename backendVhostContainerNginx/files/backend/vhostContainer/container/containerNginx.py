'''
Created on 03.05.2010

@author: toby
'''

import os, sys
from re import *
from Cheetah.Template import Template
from subprocess import call
from vhostContainer.container.containerDefault import containerDefault

class containerNginx(containerDefault):
    '''
    classdocs
    '''
    def setVars(self):
        self.template = self.env.config.get('containerNginxtemplate')
        self.vhostpath = os.path.abspath(self.env.config.get('containerNginxvhostpath'))
        self.fileprefix = self.env.config.get('containerNginxfileprefix')
        self.ipandportprefix = self.env.config.get('containerNginxipandportprefix')
        self.reloadcommand = self.env.config.get('containerNginxreloadcommand')
        
        self.vars['logpath'] = self.env.config.get('containerNginxlogPath')
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
                self.env.logger.append("restart Nginx failed")
                return False
            else:
                self.env.logger.append("restart Nginx ok")
                return True
        except OSError, e:
            elf.env.logger.append("restart Nginx failed")
            return False
