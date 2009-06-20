import MySQLdb

class database(object):

    def __init__ (self, configfile):
        self.config = {}
        self.read(configfile)
        
        self.connect()
        
    def read(self, configfile):
        
        for line in open(configfile, 'r'):
            vals = line.split()
            
            if len(vals) == 3:
                var = vals[0].replace("$","")
                val = vals[2].replace(";","")
                
                self.config[var] = val.replace("'","")
                
            if vals.count("(!defined('WCF_N'))") == 1:
                self.wcfnr = vals[3].replace(");", "")
                
        # TODO get this value also from configfile, but to begin this will do
        self.cpnr = self.wcfnr+'_1'

    def connect(self):
        self.connection = MySQLdb.connect(host=self.config['dbHost'],\
                                          db=self.config['dbName'],\
                                          user=self.config['dbUser'],\
                                          passwd=self.config['dbPassword'])

    def query(self, query):
        c = self.connection.cursor()

        c.execute(query)

        return c.fetchall()
    
    def queryDict(self, query):
        c = MySQLdb.cursors.DictCursor(self.connection)

        c.execute(query)

        return c.fetchall()
    

    def querySingle(self, query):
        c = self.connection.cursor()

        c.execute(query)

        return c.fetchone()

    def insert(self, query):
        c = self.connection.cursor()

        return c.execute(query)

