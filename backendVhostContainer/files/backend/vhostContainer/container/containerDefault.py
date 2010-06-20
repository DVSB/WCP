'''
Created on 03.05.2010

@author: toby
'''

class containerDefault(object):
    '''
    classdocs
    '''
    def __init__(self, domain, env):
        self.env = env
        self.domain = domain
        
        self.vars = {}
        self.file = None
        self.vhostpath = None
        self.fileprefix = None
        self.reloadcommand = None
        self.template = None
        self.vars['logpath'] = {'access': None, 'error': None}
        self.parsedTemplate = None
        
        self.setVars()
        
    def setVars(self):
        return "implement me"
    
    def createDomain(self):
        return "implement me"

    def updateDomain(self):
        return "implement me"
                
    def deleteDomain(self):
        return "implement me"
    
    def finishContainer(self):
        return "implement me"