---
layout: v2/modules-file
title: The File Module
prev: '<a href="../../modules/failure/expectsFailure.html">Prev: expectsFailure()</a>'
next: '<a href="../../modules/file/fromFile.html">Next: fromFile()</a>'
updated_for_v2: true
---

# The File Module

## Introduction

The __File__ module allows you to work with temporary files on disk.

It currently doesn't do anything more than this because, at DataSift, we normally run tests against virtual machines - we're seldom testing software that's installed on the same machine that Storyplayer is running on.  If Storyplayer finds a wider audience, we'll happily turn the _File_ module into something more capable.  Pull requests are welcome too :)

The source code for this module can be found in these PHP classes:

* `Prose\FromFile`
* `Prose\UsingFile`

## Dependencies

This module has no dependencies.

## Using The File Module

The basic format of an action is:

{% highlight php startinline %}
MODULE()->ACTION();
{% endhighlight %}

where __module__ is one of:

* _[fromFile()](fromFile.html)_ - get information about a file
* _[usingFile()](usingFile.html)_ - work with files

and __action__ is one of the methods available on the __module__ you choose.

Here are some examples:

{% highlight php startinline %}
$tmpName = fromFile()->getTmpFileName();
{% endhighlight %}

{% highlight php startinline %}
usingFile()->removeFile($tmpName);
{% endhighlight %}