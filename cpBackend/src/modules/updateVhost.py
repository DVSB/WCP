'''
Created on 02.05.2010

@author: toby
'''

from framework.basishandler import basishandler
from vhostContainer.vhostHandler import vhostHandler

class updateVhost(basishandler):
    '''
    classdocs
    '''
    
    def run(self):
        self.vhandler = vhostHandler(self.db, self.config)
        
        #update this domain
        if self.data.has_key('domainID'):
            self.vhandler.addDomain(self.data['domainID'])
            
        #update this domains
        elif self.data.has_key('domainIDs'):
            for d in self.data['domainIDs']:
                self.vhandler.addDomain(d)
        
        #update all domains with this vhosts
        elif self.data.has_key('vhostID'):
            self.vhandler.addDomainsForVhost(self.data['vhostID'])
        
        #update all domains of this user
        elif self.data.has_key('userID'):
            self.vhandler.addDomainsForUser(self.data['userID'])
            
        else:
            return 'invalid'
        
        self.vhandler.updateVhosts()
        
        return 'success'
        