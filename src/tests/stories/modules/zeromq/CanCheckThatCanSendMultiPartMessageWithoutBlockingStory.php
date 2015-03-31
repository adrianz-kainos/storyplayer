<?php

// ========================================================================
//
// STORY DETAILS
//
// ------------------------------------------------------------------------

$story = newStoryFor('Storyplayer')
         ->inGroup(['Modules', 'ZeroMQ'])
         ->called('Can check can send a multi-part message without blocking');

$story->requiresStoryplayerVersion(2);

// ========================================================================
//
// PRE-TEST PREDICTION
//
// ------------------------------------------------------------------------

$story->addTestCanRunCheck(function() {
	// do we have the ZMQ extension installed?
	expectsZmq()->requirementsAreMet();
});

// ========================================================================
//
// STORY SETUP / TEAR-DOWN
//
// ------------------------------------------------------------------------

$story->addTestSetup(function() {
	// let's decide on the message we're sending and expecting back
	$checkpoint = getCheckpoint();
	$checkpoint->expectedMessage = [ "hello, Storyplayer", "you're looking fine today"];
	$checkpoint->actualMessage = null;
});

// ========================================================================
//
// PRE-TEST PREDICTION
//
// ------------------------------------------------------------------------

// ========================================================================
//
// PRE-TEST INSPECTION
//
// ------------------------------------------------------------------------

// ========================================================================
//
// POSSIBLE ACTION(S)
//
// ------------------------------------------------------------------------

$story->addAction(function() {
	// we're going to store the received message in here
	$checkpoint = getCheckpoint();

	foreach(firstHostWithRole("zmq_target") as $hostId) {
		$context = usingZmqContext()->getZmqContext();
		$inPort  = fromHost($hostId)->getStorySetting("zmq.multi.inPort");
		$outPort = fromHost($hostId)->getStorySetting("zmq.multi.outPort");

		$inSocket  = usingZmqContext($context)->connectToHost($hostId, $inPort, 'PUSH');
		$outSocket = usingZmqContext($context)->connectToHost($hostId, $outPort, 'PULL');

		expectsZmqSocket($inSocket)->canSendmultiNonBlocking($checkpoint->expectedMessage);
		$checkpoint->actualMessage = fromZmqSocket($outSocket)->recvMulti();
	}
});

// ========================================================================
//
// POST-TEST INSPECTION
//
// ------------------------------------------------------------------------

$story->addPostTestInspection(function() {
	$checkpoint = getCheckpoint();

	assertsObject($checkpoint)->hasAttribute("expectedMessage");
	assertsObject($checkpoint)->hasAttribute("actualMessage");
	assertsArray($checkpoint->actualMessage)->equals($checkpoint->expectedMessage);
});