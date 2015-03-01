---
layout: v2/modules-assertions
title: Double Assertions
prev: '<a href="../../modules/assertions/assertsBoolean.html">Prev: Boolean Assertions</a>'
next: '<a href="../../modules/assertions/assertsInteger.html">Next: Integer Assertions</a>'
---

# Double Assertions

_assertsDouble()_ allows you to test a PHP double or float, and to compare it against another PHP double or float.

The source code for these actions can be found in the class `Prose\AssertsDouble`.

## doesNotEqual()

Use `assertsDouble()->doesNotEqual()` to make sure that two floating point numbers are not the same.

{% highlight php startinline %}
$expected = 1.1;
$actual   = 1.0;
assertsDouble($actual)->doesNotEqual($expected);
{% endhighlight %}

See _[equals()](#equals)_ for a discussion of how this test works.

## equals()

Use `assertsDouble()->equals()` to make sure that two floating point numbers are the same.

{% highlight php startinline %}
$expected = 1.1;
$actual   = 1.1;
assertsDouble($actual)->equals($expected);
{% endhighlight %}

If the test fails, Storyplayer's output will show the differences between the two numbers.

## isDouble()

Use `assertsDouble()->isDouble()` to make sure that something really is a floating point number.

{% highlight php startinline %}
$data = 1.1;
assertsDouble($data)->isDouble();
{% endhighlight %}

This is most often used in the [post-test inspection phase](../../stories/post-test-inspection.html) to validate the data in the [checkpoint](../../stories/the-checkpoint.html):

{% highlight php startinline %}
$story->addAction(function() {
    // get the checkpoint
    $checkpoint = getCheckpoint();

    // store the final balance for checking later
    $checkpoint->balance = 99.99;
});

$story->addPostTestInspection(function() {
    // get the checkpoint
    $checkpoint = getCheckpoint();

    // make sure the checkpoint contains
    // the final balance
    assertsObject($checkpoint)->hasAttribute("balance");
    assertsDouble($checkpoint->balance)->isDouble();
});
{% endhighlight %}

## isEmpty()

Use `assertsDouble()->isEmpty()` to make sure that a variable is empty.

{% highlight php startinline %}
$data = 0;
assertsDouble($data)->isEmpty();
{% endhighlight %}

## isGreaterThan()

Use `assertsDouble()->isGreaterThan()` to make sure that a floating point number is larger than a value you provide.

{% highlight php startinline %}
$data = 1.1;
assertsDouble($data)->isGreaterThan(1.0);
{% endhighlight %}

## isGreaterThanOrEqualTo()

Use `assertsDouble()->isGreaterThan()` to make sure that a floating point number is at least a value you provide.

{% highlight php startinline %}
$data = 1.1;
assertsDouble($data)->isGreaterThanOrEqualTo(1.1);
{% endhighlight %}

## isLessThan()

Use `assertsDouble()->isLessThan()` to make sure that a floating point number is smaller than a value you provide.

{% highlight php startinline %}
$data = 1.0;
assertsDouble($data)->isLessThan(1.1);
{% endhighlight %}

## isLessThanOrEqualTo()

Use `assertsDouble()->isLessThanOrEqualTo()` to make sure that a floating point number is no larger than a value you provide.

{% highlight php startinline %}
$data = 1.1;
assertsDouble($data)->isLessThanOrEqualTo(1.1);
{% endhighlight %}

## isNotEmpty()

Use `assertsDouble()->isNotEmpty()` to make sure that a floating point number is not empty.

{% highlight php startinline %}
$data = 1.1;
assertsDouble($data)->isNotEmpty();
{% endhighlight %}

## isNull()

Use `assertsDouble()->isNull()` to make sure that the PHP variable is actually NULL, rather than a floating point number.

{% highlight php startinline %}
$data = null;
assertsDouble($data)->isNull()
{% endhighlight %}

This has been added for completeness; we'd always recommend using _[isDouble()](#isdouble)_ instead of testing for NULL.

## isNotNull()

Use `assertsDouble()->isNotNull()` to make sure that the PHP variable is not NULL.

{% highlight php startinline %}
$data = 1.1;
assertsDouble($data)->isNotNull();
{% endhighlight %}

This has been added for completeness; we'd always recommend using _[isDouble()](#isdouble)_ instead of testing for NULL.