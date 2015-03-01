---
layout: v2/modules-hoststable
title: The HostsTable Module
prev: '<a href="../../modules/host/usingHost.html">Prev: usingHost()</a>'
next: '<a href="../../modules/hoststable/how-hosts-are-remembered.html">Next: How Hosts Are Remembered</a>'
---

# The HostsTable Module

The __HostsTable__ module allows you to interact with Storyplayer's internal list of hosts that it knows about, such as [Vagrant](../vagrant/index.html) virtual machines.

The source code for this Prose module can be found in these PHP classes:

* `Prose\ExpectsHostsTable`
* `Prose\FromHostsTable`
* `Prose\UsingHostsTable`

<div class="callout warning" markdown="1">
#### Internal Module

This module is used internally by Storyplayer. Do not call this module from your stories.
</div>

## Dependencies

This module has no dependencies.

## Using The HostsTable Module

The basic format of an action is:

{% highlight php startinline %}
MODULE()->ACTION();
{% endhighlight %}

where __module__ is one of:

* _fromHostsTable()_ - retrieve entries from the hosts table
* _expectsHostTable()_ - ensure entries exist in the hosts table
* _usingHostsTable()_ add and remove entries to and from the hosts table

and __action__ is one of the methods available on the __module__ you choose.

This module is really here for use by other modules (an internal API, if you like), but you can use it from your own tests if you wish.