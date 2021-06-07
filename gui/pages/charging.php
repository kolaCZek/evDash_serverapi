<?php
	$dta = $gui->getChargingList($_SESSION['uid']);
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

	$sumkWh = 0;
?>
<div class="order-md-2 mb-4" style="margin: auto; width: 80%">
	<h4 class="d-flex mb-3 text-muted">
		<?php if(isset($carTypes[$dta->carType])):?>
			<?= $carTypes[$dta->carType]; ?>
		<?php else: ?>
			Charging
		<?php endif; ?>
	</h4>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>Time</th>
        <th>Charged</th>
        <th>Percent</th>
        <th>AC/DC</th>
        <th>Location</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($dta as $item): ?>
        <tr>
          <td>
						<a href="?p=charging_detail&amp;id=<?= $item->iddata ?>">
							<?= date('Y-m-d H:i:s',strtotime($item->timestamp.' UTC')) ?>
						</a>
					</td>
          <td><?= round($item->kwh, 2); ?> kWh</td>
					<?php $sumkWh += $item->kwh; ?>
          <td><?= $item->min_perc ?>% &gt; <?= $item->max_perc ?>%</td>
					<td>
						<?php if($item->is_dc): ?>
							DC
						<?php else: ?>
							AC
						<?php endif; ?>
					</td>
					<td>
						<?php if($item->gps_lat && $item->gps_lon): ?>
							<a href="http://www.google.com/maps/place/<?= $item->gps_lat ?>,<?= $item->gps_lon ?>" target="_blank">
								<?= $item->gps_lat ?>, <?= $item->gps_lon ?>
							</a>
						<?php else: ?>
							/
						<?php endif; ?>
					</td>
        </tr>
      <?php endforeach; ?>
			<tr>
				<th>Sum</th>
				<td><?= round($sumkWh, 2); ?> kWh</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
    </tbody>
  </table>
</div>
