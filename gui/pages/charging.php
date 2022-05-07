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
		5 => 'Renault Zoe 22kWh',
		6 => 'Kia Niro PHEV 8.9kWh',
		7 => 'BMW I3 (2014) 22kWh',
		8 => 'Kia Soul (2020) 64kWh',
		9 => 'VW ID3 (2021) 45kWh',
		10 => 'VW ID3 (2021) 58kWh',
		11 => 'VW ID3 (2021) 77kWh',
		12 => 'Hyundai IONIQ5 58kWh',
		13 => 'Hyundai IONIQ5 72kWh',
		14 => 'Hyundai IONIQ5 77kWh',
		15 => 'Peugot e208 50kWh',
		16 => 'Kia EV6 58kWh',
		17 => 'Kia EV6 77kWh',
		18 => 'SKODA ENYAQ 55kWh',
		19 => 'SKODA ENYAQ 62kWh',
		20 => 'SKODA ENYAQ 82kWh',
		21 => 'VW ID4 (2021) 45kWh',
		22 => 'VW ID4 (2021) 58kWh',
		23 => 'VW ID4 (2021) 77kWh',
		24 => 'AUDI Q4 35kWh',
		25 => 'AUDI Q4 40kWh',
		26 => 'AUDI Q4 45kWh',
		27 => 'AUDI Q4 50kWh'
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
