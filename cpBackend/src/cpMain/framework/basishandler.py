from framework.PHPUnserialize import PHPUnserialize

class basishandler(object):
    def __init__(self, data, db, config):
        
        self.data = data
        
        if self.data != '':
            u = PHPUnserialize()
            self.data = u.unserialize(self.data)
            
        self.db = db
        self.config = config
        
    def run(self):
        print "implement me"