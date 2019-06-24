<?php
namespace Magecomp\Matrixrate\Model\ResourceModel\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Framework\DB\Select;
use Magento\Framework\App\ObjectManager;

class RateQuery
{
    /**
     * @var \Magento\Quote\Model\Quote\Address\RateRequest
     */
    private $request;
	
	protected $_mysql;

    /**
     * RateQuery constructor.
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     */
    public function __construct(
        RateRequest $request
	) {
        $this->request = $request;
	}

    /**
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    public function prepareSelect(Select $select,$zipRangeSet)
    {
        $select->where(
            'website_id = :website_id'
        )->order(
            ['dest_country_id DESC', 'dest_region_id DESC', 'dest_zip DESC', 'condition_from_value DESC']
        );//->limit(1);

		$postcode = $this->request->getDestPostcode();
		
		if ($zipRangeSet && is_numeric($postcode)) {
			#  Want to search for postcodes within a range
			$zipSearchString = ' AND :postcode BETWEEN dest_zip AND dest_zip_to';		
		} else {
			$zipSearchString = ' AND :postcode LIKE dest_zip';
		}
		
		$query = strcmp(strtolower('dest_city'),strtolower($this->request->getDestCity()));
		
		// Render destination condition
        $orWhere = '(' . implode(
            ') OR (',
            [
                "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = :postcode",
				"dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = ''",
				/*==================*/
				"dest_country_id = :country_id AND dest_region_id = :region_id AND $query = 0".$zipSearchString,
				"dest_country_id = :country_id AND dest_region_id = :region_id AND dest_city = ''".$zipSearchString,
				"dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = '' AND $query = 0",
				"dest_country_id = :country_id AND $query = 0 AND dest_region_id='0'".$zipSearchString,
				"dest_country_id = :country_id AND $query = 0 AND dest_region_id='0' AND dest_zip = ''",
				"dest_country_id = :country_id AND dest_region_id = '0' AND dest_city=''".$zipSearchString,
				"dest_country_id = :country_id AND dest_region_id = :region_id AND dest_city='' AND dest_zip=''",
				"dest_country_id = :country_id AND dest_region_id='0' AND dest_city='' AND dest_zip=''",
				"dest_country_id = '0' AND dest_region_id = '0' AND dest_zip=''",
				"dest_country_id = '0' AND dest_region_id = '0'".$zipSearchString,
				"dest_country_id = :country_id AND dest_region_id = :region_id".$zipSearchString,

                // Handle asterisk in dest_zip field
                "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_zip = '*'",
                "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = '*'",
                "dest_country_id = '0' AND dest_region_id = :region_id AND dest_zip = '*'",
                "dest_country_id = '0' AND dest_region_id = 0 AND dest_zip = '*'",
                "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = ''",
                "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = :postcode",
                "dest_country_id = :country_id AND dest_region_id = 0 AND dest_zip = '*'"
            ]
        ) . ')';
        $select->where($orWhere);

		// Render condition by condition name
        if (is_array($this->request->getConditionMRName()))
		{
            $orWhere = [];
            foreach (range(0, count($this->request->getConditionMRName())) as $conditionNumber) {
                $bindNameKey = sprintf(':condition_name_%d', $conditionNumber);
                $bindValueKey = sprintf(':condition_from_value_%d', $conditionNumber);
				$bindToKey = sprintf(':condition_to_value_%d', $conditionNumber);
                $orWhere[] = "(condition_name = {$bindNameKey} AND condition_value <= {$bindValueKey} AND condition_value >= {$bindToKey})";
            }

            if ($orWhere) {
                $select->where(implode(' OR ', $orWhere));
            }
        } else {
            $select->where('condition_name = :condition_name');
            $select->where('condition_from_value <= :condition_value');
			$select->where('condition_to_value >= :condition_value');
        }
		
		$select->where('website_id=?', $this->request->getWebsiteId());

		$select->order('dest_country_id DESC');
		$select->order('dest_region_id DESC');
		$select->order('dest_zip DESC');
		$select->order('condition_from_value DESC');
		
		return $select;
    }

    /**
     * @return array
     */
    public function getBindings()
    {
        $bind = [
            ':website_id' => (int)$this->request->getWebsiteId(),
            ':country_id' => $this->request->getDestCountryId(),
            ':region_id' => (int)$this->request->getDestRegionId(),
            ':postcode' => $this->request->getDestPostcode(),
			':city' => $this->request->getDestCity(),
        ];

        // Render condition by condition name
        if (is_array($this->request->getConditionMRName())) {
            $i = 0;
            foreach ($this->request->getConditionMRName() as $conditionName) {
                $bindNameKey = sprintf(':condition_name_%d', $i);
                $bindValueKey = sprintf(':condition_value_%d', $i);
                $bind[$bindNameKey] = $conditionName;
                $bind[$bindValueKey] = $this->request->getData($conditionName);
                $i++;
            }
        } else {
            $bind[':condition_name'] = $this->request->getConditionMRName();
            $bind[':condition_value'] = $this->request->getData($this->request->getConditionMRName());
        }
		
	ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r(['Get Bindings' => $bind], true));

        return $bind;
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address\RateRequest
     */
    public function getRequest()
    {
        return $this->request;
    }
}
