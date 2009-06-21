import MySQLdb

class database(object):

    def __init__ (self, wcfconfig):
        self.config = wcfconfig
        self.cpnr = self.config.cpnr
        self.wcfnr = self.config.wcfnr
        self.connect()

    def connect(self):
        self.connection = MySQLdb.connect(host=self.config.dbHost,\
                                          db=self.config.dbName,\
                                          user=self.config.dbUser,\
                                          passwd=self.config.dbPassword)

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

