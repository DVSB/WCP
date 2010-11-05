'''
Created on 03.05.2010

@author: toby
'''

from framework.user import user

class domain(object):
    '''
    classdocs
    '''
    
    def __init__(self, domainID, env):
        self.env = env
        self.domainID = domainID
        self.vhostContainer = None
        self.domain = []
        self.aliases = []
        self.loadDomain()
        self.getUser()
       
    def loadDomain(self):
        self.domain = self.env.db.queryDict("SELECT    * \
                                             FROM      cp" + self.env.cpnr + "_domain domain \
                                             JOIN      cp" + self.env.cpnr + "_domain_option_value domain_option \
                                                    ON (domain_option.domainID = domain.domainID) \
                                             WHERE     domain.domainID = " + str(self.domainID) 
                                           )[0]
        
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
            elif o[2] == 'integer' or o[0].find('ID') == (len(o[0]) - 2): #is real integer or ends with ID
                if self.domain[optionName] == '' or self.domain[optionName] == None:
                    var = 0
                else:
                    var = int(self.domain[optionName])
            elif o[2] == 'float':
                var = float(self.domain[optionName])
            else:
                var = self.domain[optionName]
            
            self.domain[o[0]] = var
            del self.domain[optionName]
            
            if o[0] == 'aliasDomainID':
                self.aliasDomainOption = optionName
            
        #TODO: load data from parentdomain, if given, and merge it with data of this domain
        #empty fields should be filled with data from parent
        if self.domain['parentDomainID'] > 0:
            parent = domain(self.domain['parentDomainID'], self.env)
            if self.domain['vhostContainerID'] == 0:
                self.domain['vhostContainerID'] = parent.get('vhostContainerID')
        
    def get(self, option):
        if self.domain.has_key(option):
            return self.domain[option]
        else:
            return None
        
    def getAliasDomains(self):
        aliases = self.env.db.query("SELECT      domainID \
                                     FROM        cp" + self.env.cpnr + "_domain_option_value domain_option \
                                     WHERE       " + self.aliasDomainOption + " = " + str(self.domainID))
        for a in aliases:
            self.aliases.append(domain(a[0], self.env))
        
    def getUser(self):
        self.user = user(self.get('userID'), self.env)