# Understanding the Flow

To better understand how the various parts of the system override other portions, and which code gets used before others, it helps to understand the Flow of a request cycle as it determines which controller to use.

## Loading a Library

When a request is made, the Loader will check to see if the library file is in a module. It determines this by looking at the requested library file.

Let's say we're loading the <tt>Auth</tt> library from the <tt>users</tt> module. We would use the following code to load it:

    $this->load->library('users/auth');

It recognizes that <tt>users</tt> could be a module. It does a quick check to see if it a <tT>users module</tt> exists. If it does, it will load the found module's library. Modules are searched for in the folders in your <tt>config/application.php</tt> config file, <tt>modules_locations</tt>. By default, this first searches your application's modules folder, then Bonfire's modules folder.

If no module is found for this library, it will then attempt to load that library from your application.

If one doesn't exist there, it will check Bonfire's <tt>libraries/</tt> folder as a last resort. This means that you can completely override one of Bonfire's provided libraries, helpers, etc. by placing one of the same name in your application's folder.

Finally, it will check CodeIgniter's appropriate folder as one last chance.

## Loading a Helper

Helpers function in much the same way as Libraries, described above, with one caveat. The system will always attempt to load helpers with a BF_ extension from the bonfire folder after trying to load a MY_ file from the application/libraries folder. This allows you to override helpers of the same name in your application that Bonfire has provided for you.