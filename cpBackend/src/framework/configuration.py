
_TRUE_VALUES = ('yes', 'true', 'enabled', 'on', 'aye', '1', 1, True)

class configuration(object):

	def __init__ (self, db):
		self.db = db
		self.read()

	def read (self):
		configs = self.db.query('SELECT optionName, categoryName, optionType, optionValue \
								FROM wcf'+self.db.wcfnr+'_option')

		self.config = {}

		for c in configs:
			section = c[1].split('.')[0]

			if self.config.has_key(section) == False:
				self.config[section] = {}

			self.config[section][c[0]] = (c[2],c[3])

	def getSection(self, section):
		if self.config.has_key(section) == True:
			return self.config[section]
		else:
			return None

	def get(self, section, option):
		if self.config.has_key(section) == True and self.config[section].has_key(option):
			config = self.config[section][option]
			if config[0] == 'boolean':
				if config[1] in _TRUE_VALUES:
					return True
				else:
					return False
			elif config[0] == 'integer':
				return int(config[1])
			else:
				return config[1]
		else:
			return None