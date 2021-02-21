<?php
	$dta = $gui->getLastRecord($_SESSION['uid']);
	$settings = $gui->getSettings($_SESSION['uid']);

	if(empty($settings->timezone)) {
		$settings->timezone = 'UTC';
	}

	date_default_timezone_set($settings->timezone);

	$carTypes = array(
		0 => 'Kia eNiro 2020 64kWh',
		1 => 'Hyundai Kona 2020 64kWh',
		2 => 'Hyundai Ioniq 2018 28kWh',
		3 => 'Kia eNiro 2020 39kWh',
		4 => 'Hyundai Kona 2020 39kWh',
		5 => 'Renault Zoe 22kWh'
	);
?>
<div class="col-md-4 order-md-2 mb-4" style="margin: auto" id="ev_status">
	<h4 class="d-flex mb-3 text-muted">
		<?php if(isset($carTypes[$dta->carType])):?>
			<?= $carTypes[$dta->carType]; ?>
		<?php else: ?>
			EV Status
		<?php endif; ?>
	</h4>
	<ul class="list-group mb-3">
		<li class="list-group-item d-flex justify-content-between lh-condensed">
			<div style="text-align: left">
				<h6 class="my-0">State of Charge</h6>
				<small class="text-muted">Last update: <?= date('Y-m-d H:i:s',strtotime($dta->timestamp.' UTC')) ?></small>
			</div>
			<span class="text-muted">
				<?php if(time() - strtotime($dta->timestamp.' UTC') > 120): ?>
					<i class="material-icons">cloud_off</i>
				<?php else: ?>
					<?php if($dta->chargingOn == 1): ?>
						<i class="material-icons">ev_station</i>
					<?php elseif($dta->ignitionOn == 1):?>
						<i class="material-icons">commute</i>
					<?php endif; ?>
				<?php endif; ?>
				<?= round($dta->socPerc, 1); ?> %
				<?php if($dta->socPercBms > 0): ?>
					(<?= round($dta->socPercBms, 1); ?> %)
				<?php endif; ?>
			</span>
		</li>
		<li class="list-group-item d-flex justify-content-between lh-condensed">
			<div style="text-align: left">
				<h6 class="my-0">Battery Power</h6>
			</div>
			<span class="text-muted"><?= round($dta->batPowerKw, 1); ?> kW</span>
		</li>
		<li class="list-group-item d-flex justify-content-between lh-condensed">
			<div style="text-align: left">
				<h6 class="my-0">Battery Current</h6>
			</div>
			<span class="text-muted"><?= round($dta->batPowerAmp, 1); ?> A</span>
		</li>
		<li class="list-group-item d-flex justify-content-between lh-condensed">
			<div style="text-align: left">
				<h6 class="my-0">Battery Voltage</h6>
			</div>
			<span class="text-muted" id="batVoltage"><?= round($dta->batVoltage, 1); ?> V</span>
		</li>
		<li class="list-group-item d-flex justify-content-between lh-condensed">
			<div style="text-align: left">
				<h6 class="my-0">Cummulative Energy</h6>
				<small class="text-muted">Charged / Discharged</small>
			</div>
			<span class="text-muted"><?= round($dta->cumulativeEnergyChargedKWh, 1); ?> / <?= round($dta->cumulativeEnergyDischargedKWh, 1); ?> kWh</span>
		</li>
		<li class="list-group-item d-flex justify-content-between lh-condensed">
			<div style="text-align: left">
				<h6 class="my-0">Battery Temperature</h6>
				<small class="text-muted">MIN / MAX / Inlet</small>
			</div>
			<span class="text-muted"><?= round($dta->batMinC, 1); ?> / <?= round($dta->batMaxC, 1); ?> / <?= round($dta->batInletC, 1); ?> &deg;C</span>
		</li>
		<li class="list-group-item d-flex justify-content-between lh-condensed">
			<div style="text-align: left">
				<h6 class="my-0">Battery Health</h6>
			</div>
			<span class="text-muted"><?= round($dta->sohPerc, 1); ?> %</span>
		</li>
		<li class="list-group-item d-flex justify-content-between lh-condensed">
			<div style="text-align: left">
				<h6 class="my-0">Aux. Batt. Voltage</h6>
			</div>
			<span class="text-muted"><?= round($dta->auxVoltage, 1); ?> V</span>
		</li>
	</ul>
</div>
<script>
	var autoLoad = setInterval(
	function ()
	{
		$("#ev_status").load(location.href+" #ev_status>*","");
	}, 10000);
</script>
