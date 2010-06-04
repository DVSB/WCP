'''
Created on 03.05.2010

@author: toby
'''

from framework.user import user

class domain(object):
    '''
    classdocs
    '''
    
    def __init__(self, domainID, vhostHandler, env):
        self.env = env
        self.vhostHandler = vhostHandler
        self.domainID = domainID
        self.loadDomain()
        self.getVhostContainer()
        self.getUser()
       
    def loadDomain(self):
        self.domain = self.env.db.queryDict("SELECT    * \
                                             FROM      cp" + self.env.cpnr + "_domain domain \
                                             JOIN      cp" + self.env.cpnr + "_domain_option_values domain_option \
                                                    ON (domain_option.domainID = domain.domainID) \
                                             WHERE     domain.domainID = " + self.domainID 
                                           )
        
        options = self.env.db.query("SELECT      optionName, optionID, optionType \
                                     FROM        cp" + self.env.cpnr + "_domain_option \
                                     ORDER BY    showOrder")
        
        for o in options:
            optionName = 'domainOption' + str(o[1])
            if o[2] == 'boolean':
                if int(self.domain[optionName]) == 1:
                    var = True
                else:
                    var = False
            elif o[2] == 'integer':
                if (self.domain[optionName] == ''):
                    var = 0
                else:
                    var = int(self.domain[optionName])
            elif o[2] == 'float':
                var = float(self.domain[optionName])
            else:
                var = self.domain[optionName]
            
            self.domain[o[0]] = var
            del self.domain[optionName]
            
        #TODO: load data from parentdomain, if given, and merge it with data of this domain
        #empty fields should be filled with data from parent
        
    def get(self, option):
        if self.domain.has_key(option):
            return self.domain[option]
        else:
            return None
        
    def getVhostContainer(self):
        self.vhostContainerID = self.get('vhostContainerID')
        self.vhostContainer = self.vhostHandler.getVhostContainer(self.vhostContainerID)
        
    def getVhostContainerOption(self, option):
        if self.vhostContainer.has_key(option):
            return self.vhostContainer[option]
        
    def getUser(self):
        self.user = user(self.get('userID'), self.env)