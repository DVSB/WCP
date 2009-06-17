from framework.PHPUnserialize import PHPUnserialize

class basishandler(object):
    def __init__(self, data, db, config):
        u = PHPUnserialize()
        self.data = u.unserialize(data)
        self.db = db
        self.config = config
        
    def run(self):
        print "implement me"