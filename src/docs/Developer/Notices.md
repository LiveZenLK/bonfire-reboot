# Notices

The notices library serves two purposes:

- Allows for improved 'flash messages' that might be used for success/fail messages.
- Allows for a flexible 'Notification Center' that can store multiple status messages from multiple modules.

## Status Messages

You see them everywhere in web applications. Status messages are usually used to let the user know that they successfully saved an item, or that there was an error. They are messages that only exist for a single page load and then go away. The Notices library expands on the base CodeIgniter flash messages in the Session library to allow for multiple messages that don't have to go away on a page view.

### Creating Status Messages

You can easily create a status message using the <tt>set</tt> method.

    BF_Notices::set('My message', 'status type');

This stores a message so that it can be displayed with or without a page refresh. The first parameter is the message that you want to save.

The second parameter, status type, can be any string but you should stick to a consistent naming scheme. By default, Bonfire uses *success*, *error*, *info* and *warning*.

The third parameter is the group name to use. This optional parameter allows you to group status messages by any arbitrary string. A common usage might be to group them by module name.

### Displaying Messages

There is no HTML format enforced by the Notices library, so you can customize to your application. However, this also means you will need to set things up a little bit before first using it.

    BF_Notices::all();

 This method returns an array of all of the messages that have been set. Each message is an array of the following values:

     array(
             'time',
             'msg',
             'status',
             'group'
     	);

To display these, you might do a simple loop through the information like:

    $notices = BF_Notices::all();
    foreach ($notices as $notice)
    {
    	echo '<div class="alert alert-'. $notice['status'] .'">';
    	echo $notice['msg'];
    	echo '</div>';
    }

### Restricting the Notices

Sometimes you will want to display only a certain type of notice. There are a few methods to allow you to do this:

    $notices = BF_Notices::group($group_name);

The <tt>group()</tt> method pulls out all notices that belong to a single group. Case does not matter. If no notices exist for that group, NULL is returned.

    $notices = BF_Notices::status($status);

Similarly, the <tt>status()</tt> method will retrieve only the notices that match that particular status.

    $groups = BF_Notices::groups()

This returns a list of the groups and a count of how many notices exist for each group in an array:

    array(
    	    'group_1'	=> 5,
    	    'group_2'	=> 3
    	);

## Sorting the Notices

You can sort the messages in several different ways with the <tt>sort()</tt> method. This will sort the results of the <tt>all()</tt>, <tt>group()</tt>, and <tt>status()</tt> methods.

    BF_Notices::sort('time', 'asc');
    $notices = BF_Notices::all();

The first parameter specifies the element in the notice array to sort by. Possible values are <tt>time</tt>, <tt>msg</tt>, <tt>status</tt>, and <tt>group</tt>.

The second parameter is the direction to sort by.  Ascending is <tt>asc</tt>, and descending is <tt>desc</tt>.