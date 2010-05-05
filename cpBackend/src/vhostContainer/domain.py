'''
Created on 03.05.2010

@author: toby
'''

class domain(object):
    '''
    classdocs
    '''
    
    def __init__(self, domainID, env):
        self.env = env
        self.domainID = domainID
        self.loadDomain()
        self.getVhostContainer()
       
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
        self.options = {}
        for c in options:
            self.options[c[0]] = [c[1],c[2]]
            
        #TODO: load data from parentdomain, if given, and merge it with data of this domain
        #empty fields should be filled with data from parent
        
    def get(self, option):
        if self.domain.has_key(option):
            return self.domain[option]
        
        if self.options.has_key(option):
            o = self.options[option]
            optionName = 'domainOption' + str(o[0])
            if o[1] == 'boolean':
                if int(self.domain[optionName]) == 1:
                    return True
                else:
                    return False
            elif o[1] == 'integer':
                if (self.domain[optionName] == ''):
                    return 0
                return int(self.domain[optionName])
            elif o[1] == 'float':
                return float(self.domain[optionName])
            else:
                return self.domain[optionName]
        else:
            return None
        
    def getVhostContainer(self):
        self.vhostContainerID = self.get('vhostContainerID')
        self.vhostContainer = self.env.db.queryDict("SELECT    * \
                                                 FROM      cp" + self.env.cpnr + "_vhostContainer \
                                                 WHERE     vhostContainerID = " + str(self.vhostContainerID)
                                               )
        
    def getVhostContainerOption(self, option):
        if self.vhostContainer.has_key(option):
            return self.vhostContainer[option]