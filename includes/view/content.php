<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="lewa_kolumna">
    <!--LEWA KOLUMNA POCZĄTEK-->
    <h1 class="do_lewej">Autogas Umbau eines <?php echo $_POST['car_name'] .' '. $_POST['car_model']; ?></h1>
    <div id="gallery" class="content"> <div id="controls"></div> <div id="loading"></div> <div id="slideshow"></div> <div id="caption"></div> </div>
    <div id="thumbs">
        <ul class="thumbs noscript">
            <?php echo $imagesHtml; ?>
        </ul>
    </div>
    <div class="tech">
        <table>
            <tbody>
                <tr>
                    <td width="50%">
                        <p style="margin-top: 10px;"><b>Technische Daten des Fahrzeugs:</b></p>
                        <table>
                            <tbody>
                                <tr>
                                    <td style="text-align: right;">Hubraum:</td>
                                    <td style="padding-left: 30px;"><?php echo $displacement; ?> cm<sup>3</sup></td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Leistung:</td>
                                    <td style="padding-left: 30px;"><?php echo $power; ?>kW (<?php echo round($power * 1.35962); ?> PS)</td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Baujahr:</td>
                                    <td style="padding-left: 30px;"><?php echo $year; ?></td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Km-Laufleistung beim Autogasumbau:</td>
                                    <td style="padding-left: 30px;"><?php echo $mileage; ?> km</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td width="50%">
                        <p style="margin-top: 10px;"><b>Daten der Gasanlage:</b></p>
                        <table>
                            <tbody>
                                <tr>
                                    <td style="text-align: right;">Gastank – Art u. Größe:</td>
                                    <td style="padding-left: 30px;"><?php echo $tank_type_size; ?></td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Art der Autogasanlage:</td>
                                    <td style="padding-left: 30px;"><a href="<?php echo $brc_guid; ?>"><?php echo $brc_title; ?></a></td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Reichweite im Gasbetrieb:</td>
                                    <td style="padding-left: 30px;">ca. <?php echo $range_in_gas_mode; ?> km</td>
                                </tr>
                                <tr>
                                    <td style="width: 200px; text-align: right;">Datum der Umrüstung auf Autogas:</td>
                                    <td style="padding-left: 30px;"><?php echo $car_year.'-'.$car_month; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php echo $description; ?>
    </div>