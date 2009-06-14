
_TRUE_VALUES = ('yes', 'true', 'enabled', 'on', 'aye', '1', 1, True)

class configuration(object):

	def __init__ (self, db):
		self.db = db
		self.read()

	def read (self):
		configs = self.db.query('SELECT optionName, categoryName, optionType, optionValue, optionID \
								FROM wcf'+self.db.wcfnr+'_option')

		self.config = {}

		for c in configs:
			section = c[1].split('.')[0]

			if self.config.has_key(section) == False:
				self.config[section] = {}

			self.config[section][c[0]] = (c[2],c[3],c[4])

	def getSection(self, section):
		if self.config.has_key(section) == True:
			return self.config[section]
		else:
			return None
		
	def set(self, section, option, value):
		if self.config.has_key(section) == True and self.config[section].has_key(option):
			config = self.config[section][option]
			if config[0] == 'boolean':
				value = bool(value)
				if value:
					value = '1'
				else:
					value = '0'
			
			sql = "UPDATE wcf"+self.db.wcfnr+"_option \
					   SET optionValue = '" + str(value) + "' \
					   WHERE optionID = " + str(config[2])
			self.db.query(sql)
			
	def get(self, section, option):
		if self.config.has_key(section) == True and self.config[section].has_key(option):
			config = self.config[section][option]
			if config[0] == 'boolean':
				if config[1] in _TRUE_VALUES:
					return True
				else:
					return False
			elif config[0] == 'integer':
				if (config[1] == ''):
					return 0
				return int(config[1])
			elif config[0] == 'float':
				return float(config[1])
			else:
				return config[1]
		else:
			return None