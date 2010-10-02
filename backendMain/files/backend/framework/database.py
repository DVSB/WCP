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

    def update(self, table, params, where):
        c = self.connection.cursor()

        query = "UPDATE " + table + " \
                 SET "
        
        vars = []    
        for p in params:
            if params[p] == "nativefunc":
                query += p + ", "
            else:
                query += p + " = '%s', "
                vars.append(params[p])
        
        query = query.rstrip(", ")

        if where != "":
            query += " WHERE "
            for w in where:
                query += w +" = %s AND "
                vars.append(where[w])
        
        query = query.rstrip("AND ")

        c.execute(query, vars)

    def insert(self, table, params):
        c = self.connection.cursor()
        
        query = "INSERT INTO " + table + " SET "
        
        vars = []     
        for p in params:
            if params[p] == "nativefunc":
                query += p
            else:
                query += p + " = '%s', "
                vars.append(params[p])
                
        query = query.rstrip(", ")

        c.execute(query, vars)
        
        return int(self.connection.insert_id())
