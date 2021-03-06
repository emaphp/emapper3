<?php
namespace Acme\Statement;

/**
 * @Entity products
 */
class Product {
	/**
	 * @Id
	 * @Type int
	 * @Column product_id
	 */
	public $id;
	
	/**
	 * @Type string
	 * @Column product_code
	 */
	public $code;
	
	/**
	 * @Type string
	 */
	public $category;
	
	/**
	 * @Type string
	 */
	public $description;
	
	/**
	 * @Type float
	 */
	public $price;
	
	/**
	 * @Type string
	 */
	public $color;
	
	/**
	 * @Statement Category.findAll
	 */
	public $categories;
	
	/**
	 * @Statement Sale.productIdEquals
	 * @Param(id)
	 */
	public $sales;
	
	/**
	 * @Statement Sale.productIdNotEquals
	 * @Param(id)
	 */
	public $notSales;
}
