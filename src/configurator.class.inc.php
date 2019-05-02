<?php

/**
 *  Introduction of Traits - update back to V3 to allow for skipping of V4 which is going to be rendered obsolete by V5
 *  V5 is a large leap from V3 so V3 will not be obsoleted.
 */

namespace ideamanagement\library

class configurator {
	use configurator_v5, configurator_v4 {
		configurator_v5::domain_parts insteadof configurator_v4;
	}
}

