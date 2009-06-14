from modules.basishandler import *
from modules.test import test2

class test(basishandler):
        
    def run(self):
        t = test2.test2()
        r = t.run()
        return "success"