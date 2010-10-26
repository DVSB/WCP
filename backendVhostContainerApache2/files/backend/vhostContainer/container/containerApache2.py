'''
Created on 03.05.2010

@author: toby
'''

from vhostContainer.container.containerDefault import containerDefault

class containerApache2(containerDefault):
    '''
    classdocs
    '''
    def __init__(self, domain, env):
        self.myName = 'Apache2'
        containerDefault.__init__(self, domain, env)
