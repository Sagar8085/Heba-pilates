<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DoorAccessSocket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dooraccess:opensocket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // set some variables
        $host = "10.0.1.64";
        $port = 25005;
        // don't timeout!
        set_time_limit(0);
        // create socket
        $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
        $option = socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1) or die("Could not create socket option\n");
        // bind socket to port
        $result = socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
        // start listening for connections
        $result = socket_listen($socket, 3) or die("Could not set up socket listener\n");

        // accept incoming connections
        // spawn another socket to handle communication
        $spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
        // read client input
        $input = socket_read($spawn, 1024) or die("Could not read input\n");
        // clean up input string
        $input = trim($input);
        \Log::debug('Accepting Input');
        \Log::debug($input);
        // echo "Client Message : ".$input;
        echo "V1:005";
        // fwrite($socket, "V1:005 \r\n");
        // reverse client input and send back
        $output = strrev($input) . "\n";
        $output = "V1:005";
        socket_write($spawn, $output, strlen($output)) or die("Could not write output\n");
        // close sockets
        socket_close($spawn);
        socket_close($socket);
    }
}
