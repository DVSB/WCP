'''
Created on 03.05.2010

@author: toby
'''
import imp
import sys
import os
from vhostContainer import domain
from basisFileContainer import basisFileContainer

class vhostHandler(object):
    '''
    classdocs
    '''
    def __init__(self, env):
        self.env = env
        self.domains = []
        
    def addDomain(self, domainID):
        self.domains.append(domain(domainID, self.env))
        
    def addDomainsForUser(self, userID):
        domains = self.db.query("SELECT      domainID \
                                 FROM        cp" + self.env.cpnr + "_domain \
                                 WHERE       userID = " + userID)
        for d in domains:
            self.domains.append(domain(self.db, self.env.config, d[0]))
        
    def addDomainsForVhost(self, vhostID):
        vhostField = self.env.db.querySingle("SELECT      optionID \
                                          FROM        cp" + self.env.cpnr + "_domain_option \
                                          WHERE       optionName = 'vhostContainerID'")
        
        domains = self.env.db.query("SELECT      domainID \
                                 FROM        cp" + self.db.cpnr + "_domain_option_value \
                                 WHERE       domainOption" + str(vhostField[0]) + " = " + vhostID)
        for d in domains:
            self.domains.append(domain(d[0], self.env))
            
    def createVhosts(self):
        for domain in self.domains:
            vc = self.callContainer(domain.getVhostContainerOption('vhostType'), domain)
            vc.create()
        
    def updateVhosts(self):
        for domain in self.domains:
            vc = self.callContainer(domain.getVhostContainerOption('vhostType'), domain)
            vc.update()
        
    def deleteVhosts(self):
        for domain in self.domains:
            vc = self.callContainer(domain.getVhostContainerOption('vhostType'), domain)
            vc.delete()
        
    def callContainer(self, vhostType, domain):
        if os.access('vhostContainer/container/' + vhostType + '.py', os.F_OK):
            try:
                container = self.loadContainer(vhostType)
                func = getattr(container, vhostType)
                return func(domain, self.env, vhostType)
            except Exception, e:
                return "error"
        else:
            return basisFileContainer(domain, self.env, vhostType)
        
    def loadContainer(self, name):                
        # Fast path: see if the module has already been imported.
        
        try:
            return sys.modules[name]
        except KeyError:
            pass
        
        name = "vhostContainer/container/" + name

        # If any of the following calls raises an exception,
        # there's a problem we can't handle -- let the caller handle it.
        fp, pathname, description = imp.find_module(name)
    
        try:
            return imp.load_module(name, fp, pathname, description)
        finally:
            # Since we may exit via an exception, close fp explicitly.
            if fp:
                fp.close()