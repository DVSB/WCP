'''
Created on 03.05.2010

@author: toby
'''
import imp
import sys
import os
from vhostContainer import vhostContainer
from domain import domain

class vhostHandler(object):
    '''
    classdocs
    '''
    def __init__(self, env):
        self.env = env
        self.domains = []
        self.vhostContainer = []
        self.domainIDs = []
        vhostContainers = self.env.db.queryDict("SELECT      * \
                                                FROM        cp" + self.env.cpnr + "_vhostContainer \
                                                WHERE       isContainer = 1")
        for v in vhostContainers:
            self.vhostContainer.append(vhostContainer(v, self.env))
            
    def addDomainToVhost(self, domain):
        if domain.get('vhostContainerID') == None:
            self.env.logger.append('Domain ' + domain.get('domainname') + ' has no VHostContainerID => ignore Domain!')
            return
        
        for v in self.vhostContainer:
            if int(v.get('vhostContainerID')) == int(domain.get('vhostContainerID')):
                v.addDomain(domain)
                self.env.logger.append('Domain ' + domain.get('domainname') + ' will be handled by ' + v.get('vhostName'))
                break
        
    def addDomain(self, domainID):
        if self.domainIDs.count(domainID) == 0:
            self.addDomainToVhost(domain(domainID, self.env))
            self.domainIDs.append(domainID)
        
    def addDomainsForUser(self, userID):
        domains = self.env.db.query("SELECT      domainID \
                                     FROM        cp" + self.env.cpnr + "_domain \
                                     WHERE       userID = " + userID)
        for d in domains:
            if self.domainIDs.count(domainID) == 0:
                self.addDomainToVhost(domain(d[0], self.env))
                self.domainIDs.append(d[0])
        
    def addDomainsForVhost(self, vhostID):
        vhostField = self.env.db.querySingle("SELECT      optionID \
                                              FROM        cp" + self.env.cpnr + "_domain_option \
                                              WHERE       optionName = 'vhostContainerID'")
        
        domains = self.env.db.query("SELECT      domainID \
                                     FROM        cp" + self.env.cpnr + "_domain_option_value \
                                     WHERE       domainOption" + str(vhostField[0]) + " = " + str(vhostID))
        for d in domains:
            if self.domainIDs.count(d[0]) == 0:
                self.addDomainToVhost(domain(d[0], self.env))
                self.domainIDs.append(d[0])
                
                # get all subdomains for this domain
                subdomains = self.env.db.query("SELECT      domainID \
                                                 FROM        cp" + self.env.cpnr + "_domain \
                                                 WHERE       parentDomainID = " + str(d[0]))
                for s in subdomains:
                    if self.domainIDs.count(s[0]) == 0:
                        self.addDomainToVhost(domain(s[0], self.env))
                        self.domainIDs.append(s[0])
            
    def createVhosts(self):
        for v in self.vhostContainer:
            v.createVhosts()
        
    def deleteVhosts(self):
        for v in self.vhostContainer:
            v.deleteVhosts()
