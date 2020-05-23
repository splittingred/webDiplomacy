<?php
use Illuminate\Database\Capsule\Manager as Capsule;

if (php_sapi_name() !== 'cli') {
    http_response_code(404);
    die();
}

Capsule::schema()->create('wd_Games', function ($table) {
    /** @var \Illuminate\Database\Schema\Blueprint $table */

    $table->unsignedSmallInteger('variantID')->first();
    $table->mediumIncrements('id');
    $table->unsignedSmallInteger('turn')->default(0);
    $table->enum('phase', ['Finished','Pre-game','Diplomacy','Retreats','Builds']);
    $table->unsignedInteger('processTime')->nullable()->default(null);
    $table->mediumInteger('pot')->nullable()->default(null);
    $table->string('name', 50);
    $table->enum('gameOver', ['No', 'Won', 'Drawn'])->default('No');
    $table->enum('processStatus', ['Not-processing','Processing','Crashed','Paused'])->default('Not-processing');
    $table->string('password', 16); // change to VARBINARY(16) in migration: https://laracasts.com/discuss/channels/eloquent/varbinary-equivalent-in-eloquent
    $table->enum('potType', ['Winner-takes-all','Points-per-supply-center','Unranked','Sum-of-squares'])->default('Unranked');
    $table->unsignedMediumInteger('pauseTimeRemaining')->nullable()->default(null);
    $table->mediumInteger('minimumBet')->nullable()->default(null);
    $table->unsignedSmallInteger('phaseMinutes')->default(0);
    $table->integer('phaseSwitchPeriod')->default(-1);
    $table->enum('anon', ['Yes', 'No'])->default('No');
    $table->enum('pressType', ['Regular','PublicPressOnly','NoPress','RulebookPress'])->default('Regular');
    $table->unsignedSmallInteger('attempts')->default(0);
    $table->enum('missingPlayerPolicy', ['Normal','Strict','Wait'])->default('Normal');
    $table->unsignedMediumInteger('directorUserID')->default(0);
    $table->enum('drawType', ['draw-votes-public','draw-votes-hidden'])->default('draw-votes-public');
    $table->unsignedSmallInteger('minimumReliabilityRating')->default(0);
    $table->unsignedInteger('excusedMissedTurns')->default(1);
    $table->unsignedInteger('finishTime')->nullable()->default(null);
    $table->enum('playerTypes', ['Members','Mixed','MemberVsBots'])->default('Members');
    $table->unsignedInteger('startTime')->nullable()->default(null);

    $table->primary('id');
    $table->unique('name', 'gname');
    $table->index(['processStatus', 'processTime'], 'processStatus');
    $table->index('minimumBet', 'minimumBet');
    $table->index('turn', 'turn');
    $table->index('phase', 'phase');
    $table->index('pot', 'pot');
    $table->index('password', 'password');
    $table->index(['potType', 'turn'], 'potType');
    $table->index(['potType', 'id'], 'potType_2');
    $table->index(['potType', 'pot'], 'potType_3');
    $table->index(['phase', 'turn'], 'phase_2');
    $table->index(['phase', 'minimumBet'], 'phase_3');
    $table->index(['phase', 'id'], 'phase_4');
    $table->index(['phase', 'pot'], 'phase_5');
    $table->index(['phase', 'password'], 'phase_6');
    $table->index(['phase','phaseMinutes'], 'phase_7');
    $table->index('phaseMinutes', 'phaseMinutes');
    $table->index('anon', 'anon');
    $table->index('pressType', 'pressType');
});