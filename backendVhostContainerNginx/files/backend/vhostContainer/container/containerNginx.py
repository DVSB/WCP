'''
Created on 03.05.2010

@author: toby
'''

from vhostContainer.container.containerDefault import containerDefault

class containerNginx(containerDefault):
    '''
    classdocs
    '''
    def __init__(self, domain, env):
        self.myName = 'Nginx'
        containerDefault.__init__(self, domain, env)