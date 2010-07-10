'''
Created on 03.05.2010

@author: toby
'''
import imp
import sys
import os
from vhostContainer import domain, vhostContainer

class vhostHandler(object):
    '''
    classdocs
    '''
    def __init__(self, env):
        self.env = env
        self.domains = []
        self.vhostContainer = []
        vhostContainer = self.env.db.queryDict("SELECT      * \
                                                FROM        cp" + self.env.cpnr + "_vhostContainer \
                                                WHERE       isContainer = 1")
        for v in vhostContainer:
            self.vhostContainer.append(vhostContainer(v))
            
    def addDomainToVhost(self, domain):
        for v in self.vhostContainer:
            if v.get('vhostContainerID') == domain.get('vhostContainerID'):
                v.addDomain(domain)
        
    def addDomain(self, domainID):
        self.addDomainToVhost(domain(domainID, self, self.env))
        
    def addDomainsForUser(self, userID):
        domains = self.env.db.query("SELECT      domainID \
                                     FROM        cp" + self.env.cpnr + "_domain \
                                     WHERE       userID = " + userID)
        for d in domains:
            self.addDomainToVhost(domain(d[0], self, self.env))
        
    def addDomainsForVhost(self, vhostID):
        vhostField = self.env.db.querySingle("SELECT      optionID \
                                              FROM        cp" + self.env.cpnr + "_domain_option \
                                              WHERE       optionName = 'vhostContainerID'")
        
        domains = self.env.db.query("SELECT      domainID \
                                     FROM        cp" + self.db.cpnr + "_domain_option_value \
                                     WHERE       domainOption" + str(vhostField[0]) + " = " + vhostID)
        for d in domains:
            self.addDomainToVhost(domain(d[0], self, self.env))
            
    def createVhosts(self):
        for v in self.vhostContainer:
            v.createDomains()
        
    def updateVhosts(self):
        for v in self.vhostContainer:
            vc.updateDomains()
        
    def deleteVhosts(self):
        for v in self.vhostContainer:
            v.deleteDomains()
        