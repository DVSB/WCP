'''
Created on 03.05.2010

@author: toby
'''
from framework.functions import loadModule

class vhostContainer(object):
    '''
    classdocs
    '''
    
    def __init__(self, data, env):
        self.env = env
        self.vhost = data
        self.domains = []
        self.myFunc = self.getContainer(self.get('vhostType'))
        
    def get(self, option):
        if self.vhost.has_key(option):
            return self.vhost[option]
        else:
            return None
        
    def addDomain(self, domain):
        domain.vhostContainer = self
        self.domains.append(domain)
        
    def createVhosts(self):
        vc = None
        for domain in self.domains:
            vc = self.myFunc(domain, self.env)
            vc.createDomain()
            
        if vc != None:
            vc.finishContainer()
        
    def updateVhosts(self):
        vc = None
        for domain in self.domains:
            vc = self.myFunc(domain, self.env)
            vc.updateDomain()
            
        if vc != None:
            vc.finishContainer()
        
    def deleteVhosts(self):
        vc = None
        for domain in self.domains:
            vc = self.myFunc(domain, self.env)
            vc.deleteDomain()
            
        if vc != None:
            vc.finishContainer()
        
    def getContainer(self, vhostType):
        try:
            container = loadModule(self.myType, 'vhostContainer/container/')
            return getattr(container, self.myType)
        except Exception, e:
            self.env.logger.append(str(e))
            return "error"