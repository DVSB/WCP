'''
Created on 02.05.2010

@author: toby
'''

from framework.basishandler import basishandler
from vhostContainer.vhostHandler import vhostHandler

class deleteVhost(basishandler):
    '''
    classdocs
    '''
    
    def run(self):
        self.vhandler = vhostHandler(self.db, self.config)
        
        #delete this domain
        if self.data.has_key('domainID'):
            self.vhandler.addDomain(self.data['domainID'])
            
        #delete this domains
        elif self.data.has_key('domainIDs'):
            for d in self.data['domainIDs']:
                self.vhandler.addDomain(d)
        
        #delete all domains with this vhosts
        elif self.data.has_key('vhostID'):
            self.vhandler.addDomainsForVhost(self.data['vhostID'])
        
        #delete all domains of this user
        elif self.data.has_key('userID'):
            self.vhandler.addDomainsForUser(self.data['userID'])
            
        else:
            return 'invalid'
        
        self.vhandler.deleteVhosts()
        
        return 'success'
        