from twisted.internet.protocol import Factory, Protocol
from twisted.internet import reactor
import redis
r = redis.Redis(host='localhost', port=6379, db=15)
#ohhh this is how you comment in python
class IphoneChat(Protocol):
	def connectionMade(self):
        	self.factory.clients.append(self)
        
		print "clients are ", self.factory.clients

	def connectionLost(self, reason):
		self.factory.clients.remove(self)
	
	def dataReceived(self, data):
		b = data.strip()
		a = b.split(':')
		print a
		if len(a) > 1:
			command = a[0]
			uid = a[1]
			lid = a[2]

			
			msg = ""
			if command == "join":
				self.name = uid #this step has no use, i just felt like trying it to see if it worked
				r.sadd("loc:" + lid + ".active", self.name) #set add
				msg = data #unnecessary cuz was trying to for each already active at user's location, echo back join:alreadythereuid:lid x times
			
			if command == "left":
				self.name = uid #copy and pasted
				r.srem("loc:" + lid + ".active", self.name) #set remove
				msg = data

			for c in self.factory.clients:
				c.message(msg)

	def message(self, message):
		self.transport.write(message + '\n')

factory = Factory()
factory.protocol = IphoneChat
factory.clients = []
reactor.listenTCP(800, factory)
print "TCP actives server started"
reactor.run()
