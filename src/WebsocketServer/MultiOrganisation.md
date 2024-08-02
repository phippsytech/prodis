To ensure that messages sent through your WebSocket server are isolated and only shared within the context of each respective company, you'll need to implement some form of authentication and message filtering. Here's a general approach to achieve this using Ratchet and ReactPHP:

1. **Authentication and Company Context:**
   When a user connects to the WebSocket server, you need to establish their company context. This could involve sending some form of authentication token or credentials along with the connection request. Once the user's company is verified, you should associate the WebSocket connection with that company.

2. **Company-Specific Channels:**
   Create separate communication channels (rooms) for each company. This allows you to segregate messages based on the company context. When a user from Company A sends a message, it should only be broadcasted to users in Company A's channel.

3. **Message Filtering:**
   Modify the WebSocket message handlers to filter messages based on the company context. This way, messages sent by a user from Company A are only broadcasted to other users within Company A's channel.

Here's a high-level code structure to illustrate these steps:

```php
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class CompanyWebSocketServer implements MessageComponentInterface {
    protected $clients;
    protected $companyChannels;

    public function __construct() {
        $this->clients = new \SplObjectStorage();
        $this->companyChannels = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        // Authenticate user and get their company
        $company = $this->authenticateUserAndGetCompany($conn);

        // Create company-specific channel if not exists
        if (!isset($this->companyChannels[$company])) {
            $this->companyChannels[$company] = new \SplObjectStorage();
        }

        $this->clients->attach($conn);
        $this->companyChannels[$company]->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $company = $this->getCompanyForConnection($from);

        // Broadcast message within the company's channel
        foreach ($this->companyChannels[$company] as $client) {
            $client->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $company = $this->getCompanyForConnection($conn);

        $this->clients->detach($conn);
        $this->companyChannels[$company]->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        // Handle errors
    }

    protected function authenticateUserAndGetCompany(ConnectionInterface $conn) {
        // Authenticate user and return company
    }

    protected function getCompanyForConnection(ConnectionInterface $conn) {
        // Retrieve company associated with the connection
    }
}

$server = new \Ratchet\Server\IoServer(
    new \Ratchet\Http\HttpServer(
        new \Ratchet\WebSocket\WsServer(
            new CompanyWebSocketServer()
        )
    ),
    8080
);

$server->run();
```

Keep in mind that this is a simplified example. You would need to implement the `authenticateUserAndGetCompany` and `getCompanyForConnection` methods to handle the authentication and retrieval of the company context based on the user's connection.

By using this approach, you can ensure that messages are isolated within their respective company channels and do not leak to other companies using the same WebSocket server.