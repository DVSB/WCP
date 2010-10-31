'''
Created on 02.05.2010

@author: toby
'''

from modules.basishandler import basishandler
from vhostContainer.vhostHandler import vhostHandler

class deleteVhost(basishandler):
    '''
    classdocs
    '''
    
    def run(self):
        self.vhandler = vhostHandler(self.env)
        
        for data in self.data:
            #delete this domain
            if data.has_key('domainID'):
                self.vhandler.addDomain(data['domainID'])
                
            #delete this domains
            elif data.has_key('domainIDs'):
                for d in data['domainIDs']:
                    self.vhandler.addDomain(d)
            
            #delete all domains with this vhosts
            elif data.has_key('vhostID'):
                self.vhandler.addDomainsForVhost(data['vhostID'])
            
            #delete all domains of this user
            elif data.has_key('userID'):
                self.vhandler.addDomainsForUser(data['userID'])
                
            else:
                return 'invalid'
            
        self.vhandler.deleteVhosts()
        
        return 'success'
        