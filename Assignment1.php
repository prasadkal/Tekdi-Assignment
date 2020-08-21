<?php
$strJson = '[
  {
    "id": 8,
    "parent": 4,
    "name": "Food & Lifestyle"
  },
  {
    "id": 2,
    "parent": 1,
    "name": "Mobile Phones"
  },
  {
    "id": 1,
    "parent": 0,
    "name": "Electronics"
  },
  {
    "id": 3,
    "parent": 1,
    "name": "Laptops"
  },
  {
    "id": 5,
    "parent": 4,
    "name": "Fiction"
  },
  {
    "id": 4,
    "parent": 0,
    "name": "Books"
  },
  {
    "id": 6,
    "parent": 4,
    "name": "Non-fiction"
  },
  {
    "id": 7,
    "parent": 1,
    "name": "Storage"
  }
]';

function sortJson( $arrJsonSort ) {
	$arrLeftSide = $arrGreater = [];
	if( count( $arrJsonSort ) < 2 ) {
		return $arrJsonSort;
	}
	$pivot_key = key( $arrJsonSort );
	$pivot     = array_shift( $arrJsonSort );
	foreach( $arrJsonSort as $val ) {
		if( $val <= $pivot ) {
			$arrLeftSide[] = $val;
		} elseif( $val > $pivot ) {
			$arrGreater[] = $val;
		}
	}

	return array_merge( sortJson( $arrLeftSide ), [ $pivot_key => $pivot ], sortJson( $arrGreater ) );
}

$arrJson = json_decode( $strJson, true );
$arrJson = sortJson( $arrJson );
$arrTrav = jsonTraverse( $arrJson, 'id', 'parent' );

print_r( json_encode( $arrTrav, JSON_PRETTY_PRINT ) );

function jsonTraverse( $arrJsonTraverse, $strTraverseBy = 'id', $strTraverseFor = 'parent_id', $strTraverseNode = 'children', $strTraverseId = NULL ) {
	$arrTreeJson = [];
	foreach( $arrJsonTraverse as $index => $children ) {
		if( isset( $children[$strTraverseFor] ) && $children[$strTraverseFor] == $strTraverseId ) {
			$children[$strTraverseNode] = jsonTraverse( $arrJsonTraverse, $strTraverseBy, $strTraverseFor, $strTraverseNode, $children[$strTraverseBy] );
			$arrTreeJson[]            = $children;
		}
	}
	if( !$strTraverseId ) {
		$employeesIds      = array_column( $arrJsonTraverse, $strTraverseBy );
		$managers          = array_column( $arrJsonTraverse, $strTraverseFor );
		$missingManagerIds = array_filter( array_diff( $managers, $employeesIds ) );
		foreach( $arrJsonTraverse as $record ) {
			if( in_array( $record[$strTraverseFor], $missingManagerIds ) ) {
				$arrTreeJson[] = $record;
			}
		}
	}

	return $arrTreeJson;
}

?>
