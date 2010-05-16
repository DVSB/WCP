from framework.PHPUnserialize import PHPUnserialize

class basishandler(object):
    def __init__(self, data, env):
        
        self.data = data
        
        if self.data != '':
            u = PHPUnserialize()
            self.data = u.unserialize(self.data)
            
        self.env = env
        
    def run(self):
        print "implement me"