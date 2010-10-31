'''
Created on 03.05.2010

@author: toby
'''

import os, sys
from re import *    
from subprocess import call
from time import strftime

class containerDefault(object):
    '''
    classdocs
    '''
    def __init__(self, domain, env):
        self.env = env
        self.domain = domain
        
        self.vars = {}
        self.file = None
        self.wildcardfile = None
        self.vhostpath = None
        self.fileprefix = None
        self.filewildcardprefix = None
        self.reloadcommand = None
        self.template = None
        self.vars['logpath'] = ''
        self.vars['isWildCardTemplate'] = False
        self.vars['aliases'] = []
        self.parsedTemplate = None
        self.parsedWildCardTemplate = None
        
        self.setVars()
        
    #set vars for this domain and do some funky stuff with it
    def setVars(self):
        self.template = self.env.config.get('container' + self.myName + 'template')
        self.vhostpath = os.path.abspath(self.env.config.get('container' + self.myName + 'vhostpath'))
        self.fileprefix = self.env.config.get('container' + self.myName + 'fileprefix')
        self.filewildcardprefix = self.env.config.get('container' + self.myName + 'filewildcardprefix')
        self.ipandportprefix = self.env.config.get('container' + self.myName + 'ipandportprefix')
        self.reloadcommand = self.env.config.get('container' + self.myName + 'reloadcommand')
        
        self.vars['logpath'] = self.env.config.get('container' + self.myName + 'logPath')
        self.vars.update(self.domain.vhostContainer.vhost)
        self.vars.update(self.domain.domain)
        self.vars.update(self.domain.user.user)
        
        self.domain.getAliasDomains()
        
        self.file = os.path.abspath(self.vhostpath + '/' + self.fileprefix + '_' + str(self.domain.domainID) + '_' + self.domain.get("domainname") + '.conf')
        self.wildcardfile = os.path.abspath(self.vhostpath + '/' + self.filewildcardprefix + '_' + str(self.domain.domainID) + '_' + self.domain.get("domainname") + '.conf')
    
    #create a domain
    def createDomain(self):
        self.deleteDomain()

        if self.domain.get('isAliasDomain') == 'alias' and self.domain.get('aliasDomainID') != 0:
            self.env.logger.append('Domain ' + self.domain.get('domainname') + ' is aliasDomain => wont create Domain!')
            return
        
        if self.domain.get('noWebDomain') == True:
            self.env.logger.append('Domain ' + self.domain.get('domainname') + ' is noWebDomain => wont create Domain!')
            return
        
        self.createTemplates()
        self.createPath()
        self.parseVars()
        self.writeFile()
    
    #delete all files for this domain
    def deleteDomain(self):
        f = self.getFilePath(self.file, self.fileprefix, self.domain.domainID)
        
        if f <> False:
            os.remove(f)
            
        f = self.getFilePath(self.wildcardfile, self.filewildcardprefix, self.domain.domainID)
        
        if f <> False:
            os.remove(f)
    
    #create templateobjects, nothing really done here...
    def createTemplates(self):    
        try:
            from Cheetah.Template import Template
        except ImportError:
            self.env.logger.append("Cheetah-Template seems not to be installed! Please install python-cheetah")
            raise
        
        self.parsedTemplate = Template(self.template, searchList=[self.vars])
        
        if self.domain.get('isWildcardDomain') == True:
            self.parsedWildCardTemplate = Template(self.template, searchList=[self.vars])
            
    #last change to manipulate some vars
    def parseVars(self):
        if self.domain.get('wwwServerAlias') == True:
            self.vars['aliases'].append('www.' + self.domain.get('domainname'))
        
        if len(self.domain.aliases) > 0:
            for alias in self.domain.aliases:
                if alias.get('noWebDomain') == True:
                    continue
                
                self.vars['aliases'].append(alias.get('domainname'))

                if alias.get('wwwServerAlias') == True:
                    self.vars['aliases'].append('www.' + alias.get('domainname'))
    
    #get filepath for vhostfile
    def getFilePath(self, file, fileprefix, domainID):
        if os.access(file, os.F_OK):
            return file
        else:
            #maybe renamed, domainname is wrong?
            files = os.listdir(self.vhostpath)
            
            for file in files:
                if search(fileprefix + '_' + str(domainID) + '_.*', file) <> None:
                    return self.vhostpath + '/' + file
                
        return False
    
    #create recursive path to documentroot and give it to domainowner
    def createPath(self):
        path = os.path.normcase(self.domain.get('documentroot'))
        if os.path.exists(path) == False:
            paths = path.split(os.sep)
            wpath = os.sep
            for p in paths:
                wpath += p + os.sep
                
                if os.path.exists(wpath) == False:
                    os.mkdir(wpath)
                    gid = int(self.domain.user.get('guid'))
                    os.chown(wpath, gid, gid)
    
    #write (both, if wildcard) vhostfiles
    def writeFile(self):
        if self.parsedTemplate <> None:
            f = file(self.file, 'w')
            f.write('# autogenerated by WCP on ' + strftime("%Y-%m-%d %H:%M:%S") + "\n")
            f.write("# DO NOT CHANGE MANUALLY, ALL CHANGES WILL BE LOST NEXT TIME THIS FILE IS GENERATED!\n")
            f.write(str(self.parsedTemplate))
            f.close()
            
        if self.parsedWildCardTemplate <> None:
            self.vars['isWildCardTemplate'] = True
            f = file(self.wildcardfile, 'w')
            f.write('# autogenerated by WCP on ' + strftime("%Y-%m-%d %H:%M:%S") + "\n")
            f.write("# DO NOT CHANGE MANUALLY, ALL CHANGES WILL BE LOST NEXT TIME THIS FILE IS GENERATED!\n")
            f.write(str(self.parsedWildCardTemplate))
            f.close()
    
    #called if all domain for this vhost are done
    def finishContainer(self):
        self.writeIPandPort()    
        self.reloadServer()
    
    #create a file with additional data for webserver
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
    
    #finally reload the server and hope for the best ;)
    def reloadServer(self):
        try:
            retcode = call(self.reloadcommand, shell=True)
            if retcode <> 0:
                self.env.logger.append("restart " + self.myName + " failed")
                return False
            else:
                self.env.logger.append("restart " + self.myName + " ok")
                return True
        except OSError, e:
            elf.env.logger.append("restart " + self.myName + " failed")
            return False