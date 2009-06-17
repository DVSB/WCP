from framework.basishandler import *

class createhome(basishandler):
    
    def run(self):
        print self.data
        print self.data['userID']
            
        return "failure"