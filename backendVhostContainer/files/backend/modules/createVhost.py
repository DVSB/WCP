'''
Created on 02.05.2010

@author: toby
'''

from modules.basishandler import basishandler
from vhostContainer.vhostHandler import vhostHandler

class createVhost(basishandler):
    '''
    classdocs
    '''
    
    def run(self):
        self.vhandler = vhostHandler(self.env)
        
        #create this domain
        if self.data.has_key('domainID'):
            self.vhandler.addDomain(self.data['domainID'])
            
        #create this domains
        elif self.data.has_key('domainIDs'):
            for d in self.data['domainIDs']:
                self.vhandler.addDomain(d)
        
        #create all domains with this vhosts
        elif self.data.has_key('vhostID'):
            self.vhandler.addDomainsForVhost(self.data['vhostID'])
        
        #create all domains of this user
        elif self.data.has_key('userID'):
            self.vhandler.addDomainsForUser(self.data['userID'])
            
        else:
            return 'invalid'
        
        self.vhandler.createVhosts()
        
        return 'success'
        