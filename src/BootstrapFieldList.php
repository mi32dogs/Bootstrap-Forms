<?php
namespace ShowPro\BootstrapForms;

use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\Tab;
use SilverStripe\View\SSViewer;
use SilverStripe\Core\ClassInfo;

class BootstrapFieldList extends Extension {

	/**
	 * A list of ignored fields that should not take on Bootstrap transforms
	 * @var array
	 */
	protected $ignores = array ();

	/**
	 * Transforms all fields in the FieldList to use Bootstrap templates
	 * @return FieldList
	 */
	public function bootstrapify() {
		foreach($this->owner as $f) {
			$sng = Injector::inst()->get($f->__toString(), true, ['dummy', '']);

			if(isset($this->ignores[$f->getName()])) continue;

            // if we have a CompositeField, bootstrapify its children
            if($f instanceof CompositeField) {
                $f->getChildren()->bootstrapify();
                continue;
            }

			// If we have a Tabset, bootstrapify all Tabs
			if($f instanceof TabSet) {
				$f->Tabs()->bootstrapify();
			}

			// If we have a Tab, bootstrapify all its Fields
			if($f instanceof Tab) {
				$f->Fields()->bootstrapify();
			}

			// If the user has customised the holder template already, don't apply the default one.
			if($sng->getFieldHolderTemplate() == $f->getFieldHolderTemplate()) {
			    $className = ClassInfo::shortName($f->__toString());
				$template = "showpro\BootstrapForms\Bootstrap{$className}_holder";
				if(SSViewer::hasTemplate($template)) {
					$f->setFieldHolderTemplate($template);
				}
				else {
					$f->setFieldHolderTemplate("showpro\BootstrapForms\BootstrapFieldHolder");
				}

			}
			// If the user has customised the field template already, don't apply the default one.
			if($sng->getTemplate() == $f->getTemplate()) {
				foreach(array_reverse(ClassInfo::ancestry($f)) as $className) {
                    $shortName = ClassInfo::shortName($className);
					$bootstrapCandidate = "showpro\BootstrapForms\Bootstrap{$shortName}";
					$nativeCandidate = $shortName;
					if(SSViewer::hasTemplate($bootstrapCandidate)) {
						$f->setTemplate($bootstrapCandidate);
						break;
					}
					elseif(SSViewer::hasTemplate($nativeCandidate)) {
						$f->setTemplate($nativeCandidate);
						break;
					}


				}
			}
		}

		return $this->owner;
	}

	/**
	 * Adds this field as ignored. Should not take on boostrap transformation
	 *
	 * @param  string $field The name of the form field
	 * @return FieldList
	 */
	public function bootstrapIgnore($field) {
		$this->ignores[$field] = true;

		return $this->owner;
	}
}
