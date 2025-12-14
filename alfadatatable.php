<!DOCTYPE html>
<html lang="en">
<meta charset="utf-8"/>
<!--
-->
<script src="/libs/js/jquery.js"></script>
<script src="/libs/js/jquery-dateformat.js"></script>
<script src="/libs/datatables/datatables.js"></script>
<script src="/libs/datatables/datatables.min.js"></script>

<link rel="stylesheet" href="/libs/alfa/alfadatatable.css"/> 

<script src="/libs/alfa/alfadatatable.js"></script>

<?php
  print sprintf("<title>%s</title>\n", $GLOBALS['alfaDatatableBodyHeading']);
?>
<body class=alfaDatatableBody>
  <div class=alfaDatatableBodyBox>
    <div class=alfaDatatableBodyHeadingBox>
      <div class=alfaDatatableBodyHeading>

<?php
  $DebugFunc = 5;

  AlfaDebug(__FILE__, $DebugFunc);

  print sprintf("<h1><u>%s</u></h1>", $GLOBALS['alfaDatatableBodyHeading']);
  print "<a class=alfaDatatableBodyHeadingDate>";

  $alfaDate = new DateTime();
  $alfaDate = $alfaDate->setTimeZone(new DateTimeZone("Australia/Brisbane"));
  print $alfaDate->format("Y-m-d H:i:s");
  print "</a>\n";
?>

      </div> <!-- alfaDatatableBodyHeading -->
    </div> <!-- alfaDatatableBodyHeadingBox -->
    <div class="alfaDatatableDataBox">
<?php
  if (isset($alfaDatatableSearchCustom)) {
    print $alfaDatatableSearchCustom;
  }
?>
      <table id="alfaDatatableDataTableID" class="alfaDatatableDataTable">
        <thead class=alfaDatatableDataTableHeading>
          <tr>
<?php

//==============================================
// Build Datatable Headings
//----------------------------------------------

  $alfaDatatableColumns = '[';
  $alfaDatatableDelim = "";

  foreach( $alfaDatatableDisplayMap as $alfaDatatableMap ) {

    // Heading Setup
//    alfaDebug( sprintf("<th class='alfaDatatableDropdownHeader'>%s</th>", $alfaDatatableMap[key($alfaDatatableMap)]), 0, __FILE__.":".__LINE__);

    print sprintf("<th class='alfaDatatableDropdownHeader'>%s</th>", $alfaDatatableMap[key($alfaDatatableMap)]);

    // Build Datatable Column config
    $alfaDatatableCol = key($alfaDatatableMap);

    if ( isset($alfaDatatableColumnMods->$alfaDatatableCol) ) {
      $alfaDatatableColumnMod = $alfaDatatableColumnMods->$alfaDatatableCol;

      switch( key(get_object_vars($alfaDatatableColumnMod)) ) {

        case "convert":
          $alfaDatatableColConvert = $alfaDatatableColumnMod->{"convert"};

          switch( $alfaDatatableColConvert->{"type"} ) {

            // If its a Date format it
            case "date":
              if ( isset($alfaDatatableColConvert->{"TZConvert}"}) ) {
                $alfaDatatableConvData = alfaDateConvTZ($alfaDatatableCol);
              } else {
                $alfaDatatableConvData = $alfaDatatableCol;
              }

              $alfaDatatableColumns .= sprintf('%s{"data" : "%s", "name" : "%s",
                "render": 
                  function (data, type, row, meta) {
                    if ( data ) {
                      // Force source to be UTC
                      return $.format.toBrowserTimeZone(data + "Z", "%s");

                    } else {
                      return "";
                    }
                  },
                "defaultContent": ""}', $alfaDatatableDelim, $alfaDatatableCol, $alfaDatatableCol, $alfaDatatableColConvert->{"ToFormat"} );
              break;


            default:
              $alfaDatatableColumns .= sprintf('%s{"data" : "%s", "name" : "%s", "defaultContent": ""}', $alfaDatatableDelim, $alfaDatatableCol, $alfaDatatableCol);
              break;

          } # End Switch ColConvert

          break;


        case "html":
          $alfaDatatableColumns .= sprintf('%s{"data" : "%s", "name" : "%s",
            "wraptext": true,
            "render":
              function (data, type, row) {
                if ( data ) {
                  return "<span %s>" + data + "</span>";
                } else {
                  return "";
                }
            },
            "defaultContent": ""}', $alfaDatatableDelim, $alfaDatatableCol, $alfaDatatableCol, $alfaDatatableColumnFormat[$alfaDatatableCol] );
          break;


        default:
          $alfaDatatableColumns = sprintf('%s%s{"data" : "%s", "name" : "%s", "defaultContent": ""}', $alfaDatatableDelim, $alfaDatatableColumns, $alfaDatatableCol, $alfaDatatableCol );
          break;

      } # End Switch ColumnMod

    } else {
      $alfaDatatableColumns .= sprintf('%s{"data" : "%s", "name" : "%s", "defaultContent": ""}', $alfaDatatableDelim, $alfaDatatableCol, $alfaDatatableCol );
    }

    $alfaDatatableDelim = ",";

  } // $alfaDatatableMap

  $alfaDatatableColumns .= ']';

?>

<script>

  // Get data to display in JavaScript

  var alfaDatatableData           = JSON.parse(<?php echo json_encode($alfaDatatableJson, JSON_HEX_TAG); ?>);

  var alfaDatatableSort    = <?php 
                                    if ( ! isset($alfaDatatableSort) ) {
                                       $alfaDatatableSort = '""';
                                     }
                                    echo '[' . $alfaDatatableSort . ']'; ?>;

  var alfaDatatableColumnOrder    = <?php 
                                     if ( ! isset($alfaDatatableColumnOrder) ) {
                                       $alfaDatatableColumnOrder = 0;
                                     }
                                    echo $alfaDatatableColumnOrder; ?>;
  var alfaDatatableColumnOrderDir = <?php 
                                     if ( ! isset($alfaDatatableColumnOrderDir) ) {
                                       $alfaDatatableColumnOrderDir = "'desc'";
                                     }
                                    echo $alfaDatatableColumnOrderDir; ?>;

  //var alfaDatatableSort           = `\{ name: ${alfaDatatableColumnOrder}, dir: ${alfaDatatableColumnOrderDir}\}`;

  if ( alfaDatatableSort == "" ) {
    alfaDatatableSort           = [ alfaDatatableColumnOrder, alfaDatatableColumnOrderDir];
  }

  var alfaDatatableColumns        = <?php echo $alfaDatatableColumns; ?>;
  var alfaDatatableColumnsFilter  = <?php echo $alfaDatatableColumnsFilter; ?>;

  alfaDatatable_Display_Data();
</script>

          </tr>
        </thead> <!-- alfaDatatableDataTableHeading -->
      </table> <!-- alfaDatatableID alfaDatatableDataTable-->
    </div> <!-- alfaDatatableDataBox -->
  </div> <!-- alfaDataBodyBox -->
</body> <!-- alfaDatatableBody -->
</html>
