'''
Created on 03.05.2010

@author: toby
'''
import imp
import sys
import os
from vhostContainer import domain
from functions import loadModule

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
            vc.createDomain()
        
    def updateVhosts(self):
        for domain in self.domains:
            vc = self.callContainer(domain.getVhostContainerOption('vhostType'), domain)
            vc.updateDomain()
        
    def deleteVhosts(self):
        for domain in self.domains:
            vc = self.callContainer(domain.getVhostContainerOption('vhostType'), domain)
            vc.deleteDomain()
        
    def callContainer(self, vhostType, domain):
        try:
            container = loadModule(vhostType, 'vhostContainer/container/')
            func = getattr(container, vhostType)
            return func(domain, self.env)
        except Exception, e:
            return "error"